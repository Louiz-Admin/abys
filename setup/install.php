<?php
// Fichier: abys-ai/setup/install.php
// ATTENTION : supprimer ce fichier après l'installation !

require_once __DIR__ . '/../api/db.php';

$db = get_db();

$tables = [

'leads' => "CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(500),
    email VARCHAR(255),
    phone VARCHAR(50),
    company_name VARCHAR(255),
    secteur VARCHAR(100),
    employee_count VARCHAR(50),
    revenue_range VARCHAR(100),
    source ENUM('url','questionnaire','hybrid') DEFAULT 'url',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_secteur (secteur),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'audits' => "CREATE TABLE IF NOT EXISTS audits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    score TINYINT UNSIGNED,
    ai_provider VARCHAR(50),
    scraping_success BOOLEAN DEFAULT FALSE,
    raw_scrape_data TEXT,
    opportunities JSON,
    simulation_data JSON,
    recommendations JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    INDEX idx_lead (lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'reports' => "CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    audit_id INT NOT NULL,
    lead_id INT NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    stripe_payment_id VARCHAR(255),
    amount DECIMAL(10,2) DEFAULT 249.00,
    pdf_path VARCHAR(500),
    content JSON,
    tutorials JSON,
    action_plan JSON,
    paid_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (audit_id) REFERENCES audits(id),
    FOREIGN KEY (lead_id) REFERENCES leads(id),
    INDEX idx_token (token),
    INDEX idx_lead (lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'subscriptions' => "CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    stripe_subscription_id VARCHAR(255) UNIQUE,
    stripe_customer_id VARCHAR(255),
    plan ENUM('assistant','seo_web','seo_llm') NOT NULL,
    price DECIMAL(10,2),
    status ENUM('active','paused','cancelled') DEFAULT 'active',
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    next_billing_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    FOREIGN KEY (lead_id) REFERENCES leads(id),
    INDEX idx_lead (lead_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'assistant_knowledge' => "CREATE TABLE IF NOT EXISTS assistant_knowledge (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sector VARCHAR(100),
    question TEXT NOT NULL,
    answer LONGTEXT NOT NULL,
    ai_generated BOOLEAN DEFAULT TRUE,
    validated BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sector (sector)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'courses' => "CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    type ENUM('own','affiliate') DEFAULT 'own',
    price DECIMAL(10,2),
    duration_h TINYINT UNSIGNED DEFAULT 3,
    level VARCHAR(50) DEFAULT 'Débutant',
    content_json JSON,
    affiliate_url VARCHAR(500),
    affiliate_commission DECIMAL(5,2),
    published BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'affiliate_tools' => "CREATE TABLE IF NOT EXISTS affiliate_tools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    affiliate_url VARCHAR(500),
    description TEXT,
    use_cases VARCHAR(500),
    secteurs JSON,
    commission_pct DECIMAL(5,2) DEFAULT 0,
    commission_type ENUM('percentage','fixed') DEFAULT 'percentage',
    monthly_revenue DECIMAL(10,2) DEFAULT 0,
    click_count INT DEFAULT 0,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'seo_audits' => "CREATE TABLE IF NOT EXISTS seo_audits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    subscription_id INT,
    platform ENUM('chatgpt','perplexity','google_ai','bing_copilot') NOT NULL,
    presence_score TINYINT UNSIGNED,
    citations_found JSON,
    recommendations JSON,
    report_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id),
    INDEX idx_lead (lead_id),
    INDEX idx_platform (platform)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'payments' => "CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT,
    stripe_payment_intent VARCHAR(255),
    amount DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'EUR',
    type ENUM('report','subscription','course') NOT NULL,
    reference_id INT,
    status ENUM('pending','succeeded','failed','refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_lead (lead_id),
    INDEX idx_stripe (stripe_payment_intent)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'settings' => "CREATE TABLE IF NOT EXISTS settings (
    `key` VARCHAR(100) PRIMARY KEY,
    value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'admin_users' => "CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

];

$errors = [];
foreach ($tables as $name => $sql) {
    try {
        $db->exec($sql);
        echo "✅ Table `$name` créée.<br>";
    } catch (PDOException $e) {
        $errors[] = "❌ Erreur `$name` : " . $e->getMessage();
        echo end($errors) . "<br>";
    }
}

// Insérer les settings par défaut
$defaults = [
    'ai_provider'        => 'claude',
    'claude_key'         => '',
    'openai_key'         => '',
    'gemini_key'         => '',
    'local_ai_url'       => '',
    'stripe_pk'          => '',
    'stripe_sk'          => '',
    'stripe_webhook'     => '',
    'price_report'       => '249',
    'price_assistant'    => '29',
    'price_seo_web'      => '99',
    'price_seo_llm'      => '99',
    'contact_email'      => '',
    'whatsapp_number'    => '',
    'smtp_host'          => '',
    'smtp_user'          => '',
    'smtp_pass'          => '',
    'site_name'          => 'ABYS AI',
];

$stmt = $db->prepare("INSERT IGNORE INTO settings (`key`, value) VALUES (?, ?)");
foreach ($defaults as $key => $val) {
    $stmt->execute([$key, $val]);
}
echo "✅ Paramètres par défaut insérés.<br>";

// Créer l'admin par défaut (mot de passe : abys2026 · à changer immédiatement)
$hash = password_hash('abys2026', PASSWORD_BCRYPT, ['cost' => 12]);
$stmt = $db->prepare("INSERT IGNORE INTO admin_users (username, password_hash) VALUES (?, ?)");
$stmt->execute(['admin', $hash]);
echo "✅ Compte admin créé (user: admin / pass: abys2026 · <strong>changer immédiatement !</strong>)<br>";

echo "<br><strong>⚠️ Supprimer ce fichier setup/install.php maintenant !</strong>";
