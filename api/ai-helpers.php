<?php
// Fichier: abys-ai/api/ai-helpers.php
// Fonctions IA partagées · ne pas inclure directement, toujours via analyze.php ou generate-report.php

function get_settings(PDO $db): array {
    $rows = $db->query("SELECT `key`, value FROM settings")->fetchAll();
    $out  = [];
    foreach ($rows as $r) {
        $out[$r['key']] = $r['value'];
    }
    return $out;
}

function call_ai(string $provider, string $prompt, array $settings, bool $fast = false): array {
    return match($provider) {
        'claude'  => call_claude($prompt, $settings, $fast),
        'openai'  => call_openai($prompt, $settings),
        'gemini'  => call_gemini($prompt, $settings),
        'local'   => call_local($prompt, $settings),
        default   => call_claude($prompt, $settings, $fast),
    };
}

function call_claude(string $prompt, array $settings, bool $fast = false): array {
    $key = decrypt_value($settings['claude_key'] ?? '');
    if (!$key) throw new Exception('Clé Claude manquante');

    // Haiku pour audit gratuit (3-5s), Sonnet pour rapport premium (qualité maximale)
    $model      = $fast ? 'claude-haiku-4-5' : 'claude-sonnet-4-5';
    $max_tokens = $fast ? 5000 : 8000;   // audit : journée avant/après + visibilité IA · premium : marge large
    $timeout    = $fast ? 75 : 180;      // Sonnet + 8000 tokens peut prendre 60-120s

    $body = json_encode([
        'model'      => $model,
        'max_tokens' => $max_tokens,
        'messages'   => [['role' => 'user', 'content' => $prompt]],
    ]);

    $response = http_post_ai('https://api.anthropic.com/v1/messages', $body, [
        'x-api-key: ' . $key,
        'anthropic-version: 2023-06-01',
        'content-type: application/json',
    ], $timeout);

    $data = json_decode($response, true);

    // ── Erreur explicite renvoyée par l'API Anthropic ──
    if (isset($data['type']) && $data['type'] === 'error') {
        $msg = $data['error']['message'] ?? 'erreur inconnue';
        error_log('[ABYS AI] Anthropic error: ' . $msg . ' · raw: ' . substr($response, 0, 400));
        throw new Exception('API Claude : ' . $msg);
    }
    if (!isset($data['content'][0]['text'])) {
        error_log('[ABYS AI] Réponse API sans contenu · raw: ' . substr((string)$response, 0, 400));
        throw new Exception('Réponse API Claude vide ou malformée');
    }

    // Diagnostic : troncature par la limite de tokens
    if (($data['stop_reason'] ?? '') === 'max_tokens') {
        error_log('[ABYS AI] stop_reason=max_tokens · réponse tronquée (model=' . $model . ', max=' . $max_tokens . ')');
    }

    $text = $data['content'][0]['text'] ?? '';
    return parse_ai_json($text);
}

function call_openai(string $prompt, array $settings): array {
    $key = decrypt_value($settings['openai_key'] ?? '');
    if (!$key) throw new Exception('Clé OpenAI manquante');

    $body = json_encode([
        'model'    => 'gpt-4o',
        'messages' => [
            ['role' => 'system', 'content' => 'Tu es un expert IA pour PME françaises. Réponds uniquement en JSON valide.'],
            ['role' => 'user', 'content' => $prompt],
        ],
        'max_tokens'      => 2500,
        'response_format' => ['type' => 'json_object'],
    ]);

    $response = http_post_ai('https://api.openai.com/v1/chat/completions', $body, [
        'Authorization: Bearer ' . $key,
        'Content-Type: application/json',
    ]);

    $data = json_decode($response, true);
    $text = $data['choices'][0]['message']['content'] ?? '';
    return parse_ai_json($text);
}

function call_gemini(string $prompt, array $settings): array {
    $key = decrypt_value($settings['gemini_key'] ?? '');
    if (!$key) throw new Exception('Clé Gemini manquante');

    $body = json_encode([
        'contents' => [['parts' => [['text' => $prompt]]]],
        'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 2500],
    ]);

    $url      = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key={$key}";
    $response = http_post_ai($url, $body, ['Content-Type: application/json']);
    $data     = json_decode($response, true);
    $text     = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    return parse_ai_json($text);
}

function call_local(string $prompt, array $settings): array {
    $url = rtrim($settings['local_ai_url'] ?? '', '/') . '/api/generate';
    if (!$url || $url === '/api/generate') throw new Exception('URL IA locale non configurée');

    $body     = json_encode(['model' => 'llama3', 'prompt' => $prompt, 'stream' => false]);
    $response = http_post_ai($url, $body, ['Content-Type: application/json']);
    $data     = json_decode($response, true);
    $text     = $data['response'] ?? '';
    return parse_ai_json($text);
}

function http_post_ai(string $url, string $body, array $headers, int $timeout = 45): string {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_CONNECTTIMEOUT => 15,
    ]);
    $result = curl_exec($ch);
    $err    = curl_error($ch);
    curl_close($ch);
    if ($err) throw new Exception("cURL: $err");
    return $result;
}

function parse_ai_json(string $text): array {
    // Retire les éventuelles clôtures markdown ```json ... ```
    $text = preg_replace('/^```json\s*/m', '', $text);
    $text = preg_replace('/^```\s*$/m', '', $text);
    $text = trim($text);

    // Isole le début du bloc JSON (premier {)
    $start = strpos($text, '{');
    if ($start !== false) {
        $text = substr($text, $start);
    }

    // Cas nominal : bloc complet, on coupe au dernier }
    $end = strrpos($text, '}');
    $candidate = ($end !== false) ? substr($text, 0, $end + 1) : $text;
    $data = json_decode($candidate, true);

    // Cas tronqué / bruité : réparation robuste par pile
    if (!is_array($data)) {
        $data = json_decode(repair_truncated_json($text), true);
    }

    if (!is_array($data) || !isset($data['opportunities'])) {
        error_log('[ABYS AI] parse_ai_json échec · texte brut (600c): ' . substr($text, 0, 600));
        throw new Exception('Réponse IA invalide : ' . substr($text, 0, 200));
    }
    return $data;
}

/**
 * Répare un JSON tronqué par la limite de tokens.
 * Parcourt le texte, mémorise chaque frontière de valeur (fin de chaîne,
 * fermeture d'objet/tableau, virgule) avec l'état de la pile d'ouvertures,
 * puis tente, de la plus longue à la plus courte, de refermer proprement
 * jusqu'à obtenir un JSON décodable contenant "opportunities".
 */
function repair_truncated_json(string $text): string {
    $inStr = false; $esc = false; $len = strlen($text);
    $stack = [];
    $cands = [];   // [longueur, copie_de_pile] à chaque frontière de valeur
    for ($i = 0; $i < $len; $i++) {
        $c = $text[$i];
        if ($esc) { $esc = false; continue; }
        if ($c === '\\') { $esc = true; continue; }
        if ($c === '"') {
            $inStr = !$inStr;
            if (!$inStr) { $cands[] = [$i + 1, $stack]; }   // chaîne fermée
            continue;
        }
        if ($inStr) continue;
        if ($c === '{' || $c === '[') {
            $stack[] = $c;
        } elseif ($c === '}' || $c === ']') {
            array_pop($stack);
            $cands[] = [$i + 1, $stack];
        } elseif ($c === ',') {
            $cands[] = [$i, $stack];   // frontière avant la virgule
        }
    }

    for ($k = count($cands) - 1; $k >= 0; $k--) {
        [$clen, $cstack] = $cands[$k];
        $out = rtrim(substr($text, 0, $clen));
        $out = preg_replace('/,\s*$/', '', $out);
        for ($j = count($cstack) - 1; $j >= 0; $j--) {
            $out .= ($cstack[$j] === '{') ? '}' : ']';
        }
        $d = json_decode($out, true);
        if (is_array($d) && isset($d['opportunities'])) {
            return $out;
        }
    }
    return $text;
}

function save_audit(PDO $db, int $lead_id, array $result, string $provider, bool $scraping_success): int {
    $stmt = $db->prepare("
        INSERT INTO audits (lead_id, score, ai_provider, scraping_success, opportunities, simulation_data, recommendations)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $lead_id,
        $result['score'] ?? 0,
        $provider,
        $scraping_success ? 1 : 0,
        json_encode($result['opportunities'] ?? []),
        json_encode([
            'total_time_h_week'     => $result['total_time_saved_h_week'] ?? 0,
            'total_money_eur_month' => $result['total_money_saved_eur_month'] ?? 0,
        ]),
        json_encode($result),
    ]);
    return (int)$db->lastInsertId();
}

function build_audit_prompt(string $domain, array $scrape, array $answers, bool $fast = false): string {
    if ($scrape) {
        $title   = substr($scrape['title']       ?? '', 0, 120);
        $excerpt = substr($scrape['text_excerpt'] ?? '', 0, 600);
        $hint    = $scrape['sector_hint'] ?? 'non déterminé';
        $context = "Site web analysé : {$domain}\n"
                 . "Titre de la page : {$title}\n"
                 . "Contenu extrait de la page :\n{$excerpt}\n"
                 . "Indication secteur (indice, peut être imprécis) : {$hint}\n\n"
                 . "IMPORTANT : détermine le secteur réel en te basant UNIQUEMENT sur le contenu et le titre ci-dessus, "
                 . "pas sur l'indice secteur qui peut être inexact. Si le site parle d'océan, de marine, d'environnement "
                 . "marin ou d'éducation à la mer, le secteur est Environnement & Éducation, pas Restauration.";
    } else {
        $context = "Entreprise sans site web.\n";
        foreach ($answers as $q => $a) $context .= "{$q}: {$a}\n";
    }

    $n = $fast ? 5 : 7;

    return <<<PROMPT
Expert IA PME françaises. JSON uniquement, pas de markdown.

{$context}

CATALOGUE D'OUTILS IA · 300+ solutions (pioche selon le secteur) :

Communication/Rédaction: ChatGPT(chatgpt.com), Claude(claude.ai), Gemini(gemini.google.com), Mistral(mistral.ai), Perplexity(perplexity.ai), Jasper(jasper.ai), Copy.ai(copy.ai), Writesonic(writesonic.com), Rytr(rytr.me), Hypotenuse AI(hypotenuse.ai), Wordtune(wordtune.com), Anyword(anyword.com), Peppertype(peppertype.ai), Scalenut(scalenut.com), Texta.ai(texta.ai), Narrato(narrato.io), Lex(lex.page), Sudowrite(sudowrite.com), NovelAI(novelai.net), Cowriter(cowriter.ai)

Email/CRM: HubSpot(hubspot.com), Pipedrive(pipedrive.com), Brevo(brevo.com), Mailchimp(mailchimp.com), ActiveCampaign(activecampaign.com), Lemlist(lemlist.com), Apollo(apollo.io), Salesforce Einstein(salesforce.com), Zoho CRM(zoho.com/crm), Freshsales(freshworks.com/crm), Close(close.com), Outreach(outreach.io), Salesloft(salesloft.com), Reply.io(reply.io), Instantly(instantly.ai), Woodpecker(woodpecker.co), Snov.io(snov.io), Hunter.io(hunter.io), Warmup Inbox(warmupinbox.com), Mailshake(mailshake.com)

Design/Visuels: Canva(canva.com), Adobe Firefly(firefly.adobe.com), Midjourney(midjourney.com), DALL-E(openai.com/dall-e), Looka(looka.com), Stable Diffusion(stability.ai), Leonardo.ai(leonardo.ai), Ideogram(ideogram.ai), Adobe Express(express.adobe.com), Figma AI(figma.com), Picsart AI(picsart.com), Fotor(fotor.com), Designs.ai(designs.ai), Brandmark(brandmark.io), Wepik(wepik.com), Visme(visme.co), Piktochart(piktochart.com), Snappa(snappa.com), Crello(crello.com), Kittl(kittl.com)

Automatisation: Zapier(zapier.com), Make(make.com), n8n(n8n.io), Activepieces(activepieces.com), Bardeen(bardeen.ai), Power Automate(powerautomate.microsoft.com), Pipedream(pipedream.com), Integromat(make.com), Workato(workato.com), Tray.io(tray.io), Automate.io(automate.io), Albato(albato.com), Latenode(latenode.com), Parabola(parabola.io), Pabbly Connect(pabbly.com/connect), Coda(coda.io), Airtable Automations(airtable.com), Notion Automations(notion.so), ifttt(ifttt.com), Relay.app(relay.app)

Productivité: Notion AI(notion.so), ClickUp AI(clickup.com), Monday AI(monday.com), Trello+Butler(trello.com), Linear(linear.app), Asana AI(asana.com), Todoist(todoist.com), Taskade AI(taskade.com), Height(height.app), Hive(hive.com), Basecamp(basecamp.com), Wrike AI(wrike.com), Smartsheet AI(smartsheet.com), Airtable(airtable.com), Coda AI(coda.io), Obsidian(obsidian.md), Roam Research(roamresearch.com), Logseq(logseq.com), Mem.ai(mem.ai), Supernormal(supernormal.com)

Réunions/Transcription: Fireflies.ai(fireflies.ai), Otter.ai(otter.ai), Fathom(fathom.video), tl;dv(tldv.io), Krisp(krisp.ai), Airgram(airgram.io), Avoma(avoma.com), Sembly(sembly.ai), MeetGeek(meetgeek.ai), Notta(notta.ai), Read AI(read.ai), Tactiq(tactiq.io), Vowel(vowel.com), Loom(loom.com), Zoom AI(zoom.us), Teams Copilot(microsoft.com/teams), Grain(grain.com), Gong(gong.io), Chorus(zoominfo.com/chorus), Modjo(modjo.ai)

Comptabilité/Finance: Pennylane(pennylane.com), Axonaut(axonaut.com), QuickBooks(quickbooks.intuit.com), Dext(dext.com), FreshBooks(freshbooks.com), Xero(xero.com), Sage(sage.com), Wave(waveapps.com), Zoho Books(zoho.com/books), Indy(indy.fr), Freebe(freebe.me), Tiime(tiime.fr), Agicap(agicap.com), Agendrix(agendrix.com), Fiskl(fiskl.com), Memo Bank(memo.bank), Spendesk(spendesk.com), Ramp(ramp.com), Expensify(expensify.com), Brex(brex.com)

E-commerce: Shopify Magic(shopify.com), Klaviyo(klaviyo.com), Yotpo(yotpo.com), Gorgias(gorgias.com), WooCommerce(woocommerce.com), PrestaShop AI(prestashop.com), Omnisend(omnisend.com), Recart(recart.com), Privy(privy.com), Drip(drip.com), Okendo(okendo.io), LoyaltyLion(loyaltylion.com), Attentive(attentive.com), Postscript(postscript.io), SMSBump(smsbump.com), Rebuy(rebuyengine.com), Nosto(nosto.com), Dynamic Yield(dynamicyield.com), Barilliance(barilliance.com), Bloomreach(bloomreach.com)

SEO/Marketing digital: Semrush(semrush.com), Surfer SEO(surferseo.com), Clearscope(clearscope.io), Frase(frase.io), Ahrefs(ahrefs.com), Moz(moz.com), SE Ranking(seranking.com), Mangools(mangools.com), NeuronWriter(neuronwriter.com), MarketMuse(marketmuse.com), PageOptimizer Pro(pageoptimizerpro.com), BrightEdge(brightedge.com), Conductor(conductor.com), Ubersuggest(ubersuggest.com), Answer The Public(answerthepublic.com), Keyword Hero(keywordhero.com), Screaming Frog(screamingfrog.co.uk), SiteGuru(siteguru.co), Rank Math(rankmath.com), RankIQ(rankiq.com)

Publicité/Ads: Adzooma(adzooma.com), Albert.ai(albert.ai), Smartly.io(smartly.io), Revealbot(revealbot.com), Madgicx(madgicx.com), Optmyzr(optmyzr.com), Zalster(zalster.com), WordStream(wordstream.com), Acquisio(acquisio.com), Pattern89(pattern89.com), Pencil(trypencil.com), AdCreative.ai(adcreative.ai), Predis.ai(predis.ai), Creatopy(creatopy.com), Bannersnack(bannersnack.com)

Réseaux sociaux: Buffer(buffer.com), Hootsuite(hootsuite.com), Later(later.com), Sprout Social(sproutsocial.com), Agorapulse(agorapulse.com), Publer(publer.io), SocialBee(socialbee.io), Missinglettr(missinglettr.com), ContentStudio(contentstudio.io), Flick(flick.tech), Ocoya(ocoya.com), FeedHive(feedhive.io), Typefully(typefully.com), Hypefury(hypefury.com), Postwise(postwise.ai)

Vidéo/Podcast: Descript(descript.com), Synthesia(synthesia.io), HeyGen(heygen.com), Runway(runwayml.com), Captions.ai(captions.ai), Pictory(pictory.ai), Lumen5(lumen5.com), InVideo AI(invideo.io), Fliki(fliki.ai), Steve.ai(steve.ai), Opus Clip(opus.pro), Veed.io(veed.io), Podcastle(podcastle.fm), Murf AI(murf.ai), ElevenLabs(elevenlabs.io), Soundraw(soundraw.io), Udio(udio.com), Suno(suno.com), Lalal.ai(lalal.ai), Adobe Podcast(podcast.adobe.com)

RH/Recrutement: BambooHR(bamboohr.com), Factorial(factorialhr.com), Personio(personio.com), Manatal(manatal.com), Workday AI(workday.com), Lever(lever.co), Greenhouse(greenhouse.io), Teamtailor(teamtailor.com), Recruitee(recruitee.com), Breezy HR(breezy.hr), Covey(getcovey.com), Fetcher(fetcher.ai), Beamery(beamery.com), Textio(textio.com), Pymetrics(pymetrics.ai), HireVue(hirevue.com), Kodo Survey(kodo.so), Lattice(lattice.com), 15Five(15five.com), Leapsome(leapsome.com)

Service client/Support: Intercom(intercom.com), Zendesk AI(zendesk.com), Tidio(tidio.com), Crisp(crisp.chat), Freshdesk AI(freshdesk.com), Help Scout(helpscout.com), Drift(drift.com), LiveChat(livechat.com), Chatfuel(chatfuel.com), ManyChat(manychat.com), Landbot(landbot.io), Voiceflow(voiceflow.com), Botpress(botpress.com), Rasa(rasa.com), Kommunicate(kommunicate.io), Kustomer(kustomer.com), Gladly(gladly.com), Hiver(hiversocial.com), Front(front.com), Dixa(dixa.com)

Juridique/Contrats: Legalstart(legalstart.fr), Juro(juro.com), Docusign AI(docusign.com), ContractPodAi(contractpodai.com), Luminance(luminance.com), Harvey(harvey.ai), Clio(clio.com), Ironclad(ironcladapp.com), PandaDoc(pandadoc.com), Proposify(proposify.com), Better Proposals(betterproposals.io), Qwilr(qwilr.com), GetAccept(getacceptapp.com), Oneflow(oneflow.com), Signaturit(signaturit.com)

Code/Tech: GitHub Copilot(github.com/features/copilot), Cursor(cursor.sh), Replit(replit.com), Codeium(codeium.com), Tabnine(tabnine.com), Amazon CodeWhisperer(aws.amazon.com/codewhisperer), Sourcegraph Cody(sourcegraph.com), Continue.dev(continue.dev), Aider(aider.chat), Phind(phind.com), CodeT5(huggingface.co/Salesforce/codet5), Pieces(pieces.app), Codiga(codiga.io), Mutable AI(mutable.ai), CodiumAI(codium.ai), Bugasura(bugasura.io), Warp(warp.dev), Fig(fig.io), Swimm(swimm.io), Stenography(stenography.fun)

Planning/Agenda: Reclaim.ai(reclaim.ai), Clockwise(getclockwise.com), Motion(usemotion.com), Calendly(calendly.com), Cal.com(cal.com), Savvycal(savvycal.com), Acuity Scheduling(acuityscheduling.com), Doodle(doodle.com), Chili Piper(chilipiper.com), Hubspot Meetings(hubspot.com/meetings), YouCanBookMe(youcanbook.me), Zcal(zcal.co), Vyte.in(vyte.in), Simplybook.me(simplybook.me), Appointy(appointy.com)

Présentations: Gamma.app(gamma.app), Beautiful.ai(beautiful.ai), Tome(tome.app), Pitch(pitch.com), Slides AI(slidesai.io), Plus AI(plusdocs.com), MagicSlides(magicslides.app), Decktopus(decktopus.com), SlidesGPT(slidesgpt.com), Prezi AI(prezi.com), Presentations.ai(presentations.ai), Kroma(kroma.ai), Storydoc(storydoc.com), Vev(vev.design), Genially(genially.com)

Traduction/Localisation: DeepL(deepl.com), Lilt(lilt.com), Smartling(smartling.com), Phrase(phrase.com), Lokalise(lokalise.com), Crowdin AI(crowdin.com), Transifex(transifex.com), memoQ(memoq.com), Memsource(memsource.com), Weglot(weglot.com), Bablic(bablic.com), Lingohub(lingohub.com), POEditor(poeditor.com), Locize(locize.com), GlobalLink(translations.com)

Recherche/Veille: Elicit(elicit.org), Consensus(consensus.app), Explainpaper(explainpaper.com), Semantic Scholar(semanticscholar.org), ResearchRabbit(researchrabbit.ai), Connected Papers(connectedpapers.com), Scite(scite.ai), Iris.ai(iris.ai), Undermind(undermind.ai), Feedly AI(feedly.com), Mention(mention.com), Brand24(brand24.com), Talkwalker(talkwalker.com), Brandwatch(brandwatch.com), Meltwater(meltwater.com)

Santé: Nabla(nabla.com), Suki AI(suki.ai), Nuance DAX(nuance.com), Corti(corti.ai), Abridge(abridge.com), Aidoc(aidoc.com), Viz.ai(viz.ai), PathAI(pathai.com), Tempus(tempus.com), Intelerad(intelerad.com), Veracyte(veracyte.com), Caption Health(captionhealth.com), Butterfly Network(butterflynetwork.com), Regard(regardapp.com), Ambience Healthcare(ambiencehealthcare.com)

BTP/Architecture: Autodesk AI(autodesk.com), Procore(procore.com), Buildertrend(buildertrend.com), PlanGrid(plangrid.com), Fieldwire(fieldwire.com), CoConstruct(coconstruct.com), Houzz Pro(pro.houzz.com), ArchiSnapper(archisnapper.com), Jonas Construction(jonasconstruction.com), eSub(esubonline.com), Bluebeam(bluebeam.com), Rhino 3D+AI(rhino3d.com), SketchUp AI(sketchup.com), BIM 360(autodesk.com/bim-360), Asite(asite.com)

Restauration/Food: Sunday(sundayapp.io), Lightspeed AI(lightspeedhq.com), MarketMan(marketman.com), Toast POS(pos.toasttab.com), Square for Restaurants(squareup.com/restaurants), Deliverect(deliverect.com), Otter(tryotter.com), Tillster(tillster.com), Popmenu(popmenu.com), OpenTable(opentable.com), Resy(resy.com), SevenRooms(sevenrooms.com), Me&u(me-u.com.au), Yumpingo(yumpingo.com), Plateforme(plateforme.app)

Immobilier: Propertybase(propertybase.com), Ylopo(ylopo.com), Structurely(structurely.com), Chime(chime.me), Follow Up Boss(followupboss.com), Lofty(lofty.com), BoldTrail(boldtrail.com), Roof AI(roof.ai), Rex(rexsoftware.com), Revaluate(revaluate.com), Homebot(homebot.ai), Ojo Labs(ojolabs.com), Skyline AI(skyline.ai), Entera(entera.ai), CompStak(compstak.com)

Transport/Logistique: Samsara(samsara.com), Route4Me(route4me.com), OptimoRoute(optimoroute.com), Circuit(getcircuit.com), Routific(routific.com), Onfleet(onfleet.com), Bringg(bringg.com), Shipbob(shipbob.com), ShipStation(shipstation.com), EasyPost(easypost.com), Freightos(freightos.com), Flexport(flexport.com), project44(project44.com), FourKites(fourkites.com), Locus(locus.sh)

Agriculture: Taranis(taranis.ag), Farmers Business Network(fbn.com), Granular(granular.ag), Climate FieldView(climate.com), aWhere(awhere.com), CropX(cropx.com), Arable(arable.com), Gamaya(gamaya.com), Prospera(prospera.ag), John Deere Operations Center(deere.com), Trimble Agriculture(trimble.com), AgLeader(agleader.com), Conservis(conservis.com), FarmLogs(farmlogs.com), Agworld(agworld.com)

Finance/Investissement: Agicap(agicap.com), Qonto(qonto.com), Stripe(stripe.com), Finary(finary.com), Cashstory(cashstory.com), Silae(silae.fr), Payfit(payfit.com), Lucca(lucca.fr), Payroll4Free(payroll4free.com), Gusto(gusto.com), Rippling(rippling.com), Remote(remote.com), Deel(deel.com), Papaya Global(papayaglobal.com), Velocity(velocity.app)

RÈGLES :
- Choisis les outils les PLUS ADAPTÉS au secteur détecté, pioche dans plusieurs catégories
- PRIORITÉ ABSOLUE aux outils SPÉCIALISÉS par secteur et par usage : évite ChatGPT/Gemini/Notion comme première suggestion · préfère Pennylane (compta), Axonaut (devis+facturation PME), Gorgias (SAV e-commerce), Manatal (recrutement), Brevo (email marketing français), Factorial (RH), Gamma (présentations), Fiskl (finance PME), Lemlist (prospection B2B), Buffer (social media)
- Les descriptions DOIVENT être ULTRA-CONCRÈTES et spécifiques au secteur : mentionne des tâches réelles (ex: "relance automatiquement vos impayés à J+7, J+14, J+30", "génère vos devis personnalisés en 30 secondes depuis votre catalogue", "répond à vos clients WhatsApp et email 24h/24 sans intervention humaine", "synchronise votre banque et catégorise toutes vos dépenses automatiquement"). PAS de descriptions vagues.
- VOCABULAIRE INTERDIT dans toutes les descriptions et le résumé · ces mots font peur aux non-initiés : "workflow", "B2B", "CRM", "pipeline", "prospects", "leads", "outreach", "automation", "process", "intégration", "scalable", "onboarding", "KPI", "funnel", "SaaS". À la place, utilise : "nouveaux clients" (leads/prospects), "carnet de clients" (CRM), "tâches répétitives" (workflow/automation), "suivi des ventes" (pipeline/CRM), "mise en place" (onboarding/intégration), "outils en ligne" (SaaS).
- Priorise les outils accessibles aux PME françaises (prix raisonnables, interface en français si possible)
- Chaque opportunité doit avoir un emoji représentant sa catégorie (ex: 📧 email, 📊 reporting, 🤖 automatisation, 🎨 design, 📹 vidéo, 💬 communication, 📅 planning, 💰 finance, 👥 RH, 🛒 e-commerce, 🔍 SEO, 🏗️ BTP, 🌾 agriculture, 🏥 santé, 🚚 logistique, ⚖️ juridique, 💻 code, 🎯 marketing, 🤝 service client, 📝 rédaction, 🔄 traduction, 🎤 réunions, 🏠 immobilier, 🍽️ restauration)
- Le champ sector_emoji doit représenter le secteur de l'entreprise

JSON attendu :
{"sector":"secteur","sector_label":"Label","sector_emoji":"🏭","company_size":"micro|petite|moyenne","score":<15-75>,"score_label":"Débutant|En chemin|Avancé","opportunities":[{"id":"id","rank":1,"category":"catégorie","emoji":"🤖","tool":"Outil","tool_url":"https://url.com","tool_domain":"domain.com","description":"Ce que ça fait concrètement pour cette entreprise spécifiquement","time_saved_h_week":<n>,"money_saved_eur_month":<n>,"productivity_gain_pct":<n>,"roi_12m_eur":<n>,"difficulty":"Facile|Moyen|Avancé","implementation_days":<n>,"monthly_cost_eur":<0 si gratuit, sinon prix mensuel moyen en euros>,"has_free_plan":<true|false>,"affiliate_commission_pct":<0-30>}],"total_time_saved_h_week":<n>,"total_money_saved_eur_month":<n>,"top3_free":[0,1,2],"summary":"2 phrases sur la situation IA de cette entreprise spécifiquement.","day_before":[{"time":"7h30","text":"Moment concret et pénible de SA journée actuelle, très spécifique à ce métier"},{"time":"12h00","text":"..."},{"time":"18h00","text":"..."},{"time":"21h00","text":"Le soir, la paperasse qui déborde sur la vie perso"}],"day_after":[{"time":"7h30","text":"Le même moment, transformé : ce que l'IA a fait pendant la nuit ou à sa place"},{"time":"12h00","text":"..."},{"time":"18h00","text":"..."},{"time":"21h00","text":"Le soir enfin libéré, avec le bénéfice humain concret"}],"day_verdict":"Une phrase qui résume ce que cette personne récupère vraiment dans sa vie (temps avec la famille, sérénité, fin des oublis).","creative_potential":[{"emoji":"🎨","title":"Titre court","text":"Ce que l'IA lui permet de CRÉER ou d'oser qu'il ne fait pas aujourd'hui, adapté à son métier (contenus, offres nouvelles, supports, présence)"}],"ai_visibility":{"pitch":"2 phrases : quand un client cherche ce métier dans ChatGPT, Claude ou Gemini, aujourd'hui c'est un concurrent qui est cité, pas lui. Expliquer l'enjeu simplement.","actions":["Action concrète 1 pour être cité par les IA","Action 2","Action 3"],"missed_clients_month":<estimation entière et réaliste de clients potentiels captés par les concurrents chaque mois>}}

Génère exactement {$n} opportunités pertinentes et réalistes pour ce secteur, en variant les catégories d'outils.
IMPORTANT top3_free : ce tableau DOIT toujours contenir exactement 3 indices entiers (0, 1, 2) pointant vers les 3 meilleures opportunités gratuites à présenter.

RÈGLES POUR LA JOURNÉE AVANT/APRÈS (day_before / day_after) : c'est le cœur émotionnel de l'audit.
- EXACTEMENT 4 moments, aux MÊMES horaires dans les deux listes, pour qu'on puisse comparer ligne à ligne.
- Ultra concret et propre à CE métier (un plombier n'a pas la journée d'un restaurateur ni d'un guide touristique).
- « Avant » : la réalité pénible mais sans condescendance, jamais culpabilisante.
- « Après » : ce qui change grâce aux outils cités plus haut ET à un copilote IA qui agit pour lui. Reste crédible, jamais magique.
- Parle de la VRAIE VIE : les soirées récupérées, les clients qui ne s'impatientent plus, les oublis qui disparaissent.

RÈGLES POUR creative_potential : exactement 3 entrées. L'IA ne sert pas qu'à gagner du temps, elle permet aussi de CRÉER et d'oser (nouvelles offres, meilleurs supports, présence en ligne, idées). Sois inspirant et concret pour ce métier précis.

RÈGLES POUR ai_visibility : c'est un levier majeur. Aujourd'hui les gens demandent à ChatGPT « un bon plombier à Nice » ou « que visiter à Nice avec un guide ». Explique l'enjeu sans jargon, avec 3 actions concrètes.
PROMPT;
}

// Alias pour compatibilité generate-report.php (mode premium = full Sonnet + 7 opps)
function build_premium_prompt(string $domain, array $scrape): string {
    return build_audit_prompt($domain, $scrape, [], false);
}
