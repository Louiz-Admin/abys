<?php
// Fichier: abys-ai/api/billing.php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); die(json_encode(['error' => 'Method not allowed']));
}

$input  = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$db     = get_db();

if ($action === 'save') {
    $fields = ['first_name','last_name','job_title','email','phone','company_name','address','postal_code','city','siret','vat_number','plan'];
    $data   = [];
    foreach ($fields as $f) {
        $data[$f] = substr(trim($input[$f] ?? ''), 0, 255);
    }
    $lead_id = intval($input['lead_id'] ?? 0) ?: null;

    // Upsert : si même email + plan, on met à jour
    $stmt = $db->prepare("
        INSERT INTO billing_info (lead_id, first_name, last_name, job_title, email, phone, company_name, address, postal_code, city, siret, vat_number, plan)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            first_name=VALUES(first_name), last_name=VALUES(last_name), job_title=VALUES(job_title),
            phone=VALUES(phone), company_name=VALUES(company_name), address=VALUES(address),
            postal_code=VALUES(postal_code), city=VALUES(city), siret=VALUES(siret),
            vat_number=VALUES(vat_number), lead_id=VALUES(lead_id)
    ");
    $stmt->execute([
        $lead_id,
        $data['first_name'], $data['last_name'], $data['job_title'],
        $data['email'], $data['phone'], $data['company_name'],
        $data['address'], $data['postal_code'], $data['city'],
        $data['siret'], $data['vat_number'], $data['plan'],
    ]);

    // Mettre à jour le lead si lead_id fourni
    if ($lead_id) {
        $db->prepare("UPDATE leads SET email=?, phone=?, company_name=? WHERE id=?")
           ->execute([$data['email'], $data['phone'], $data['company_name'], $lead_id]);
    }

    echo json_encode(['success' => true, 'billing_id' => $db->lastInsertId()]);
} else {
    http_response_code(400); echo json_encode(['error' => 'Action inconnue']);
}
