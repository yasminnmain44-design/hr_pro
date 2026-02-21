<?php
// index.php - الموقع الكامل مع الإدارة المدمجة (نسخة نهائية مع رابط الخريطة الجديد)

// ========== اتصال قاعدة البيانات ==========
$host = 'localhost';
$dbname = 'raya_kitchens';
$username = 'root';
$password = '';

try {
    // إنشاء اتصال PDO
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // إنشاء قاعدة البيانات إذا لم تكن موجودة
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE $dbname");
    
    // إنشاء الجداول إذا لم تكن موجودة
    $tables = [
        "CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            site_name VARCHAR(100) DEFAULT 'راية الابتكار للمطابخ',
            logo_text VARCHAR(100) DEFAULT 'راية الابتكار للمطابخ',
            logo_icon VARCHAR(50) DEFAULT 'fa-crown',
            primary_color VARCHAR(20) DEFAULT '#0a3143',
            secondary_color VARCHAR(20) DEFAULT '#d4af37'
        )",
        
        "CREATE TABLE IF NOT EXISTS home_content (
            id INT AUTO_INCREMENT PRIMARY KEY,
            badge_text TEXT,
            experience_years INT DEFAULT 15,
            projects_count INT DEFAULT 3000,
            happy_clients INT DEFAULT 97,
            branches_count INT DEFAULT 2,
            quality_title TEXT,
            quality_description TEXT,
            hero_image VARCHAR(255)
        )",
        
        "CREATE TABLE IF NOT EXISTS gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(50),
            title VARCHAR(255),
            image_path VARCHAR(255),
            sort_order INT DEFAULT 0
        )",
        
        "CREATE TABLE IF NOT EXISTS videos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255),
            youtube_url VARCHAR(255),
            sort_order INT DEFAULT 0
        )",
        
        "CREATE TABLE IF NOT EXISTS testimonials (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_name VARCHAR(100),
            client_city VARCHAR(100),
            content TEXT,
            sort_order INT DEFAULT 0
        )",
        
        "CREATE TABLE IF NOT EXISTS location (
            id INT AUTO_INCREMENT PRIMARY KEY,
            city VARCHAR(100),
            district VARCHAR(100),
            address TEXT,
            working_hours VARCHAR(255),
            map_url TEXT
        )",
        
        "CREATE TABLE IF NOT EXISTS contact (
            id INT AUTO_INCREMENT PRIMARY KEY,
            whatsapp VARCHAR(50),
            phone VARCHAR(50),
            email VARCHAR(100),
            show_whatsapp BOOLEAN DEFAULT TRUE,
            show_phone BOOLEAN DEFAULT TRUE,
            show_email BOOLEAN DEFAULT TRUE
        )",
        
        "CREATE TABLE IF NOT EXISTS social_media (
            id INT AUTO_INCREMENT PRIMARY KEY,
            platform VARCHAR(50),
            icon VARCHAR(50),
            link VARCHAR(255),
            sort_order INT DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE
        )",
        
        "CREATE TABLE IF NOT EXISTS offers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255),
            image_path VARCHAR(255),
            description TEXT,
            sort_order INT DEFAULT 0
        )",
        
        "CREATE TABLE IF NOT EXISTS workflow (
            id INT AUTO_INCREMENT PRIMARY KEY,
            step_number INT,
            title VARCHAR(100),
            description TEXT,
            icon VARCHAR(50),
            sort_order INT DEFAULT 0
        )"
    ];
    
    foreach ($tables as $table) {
        $pdo->exec($table);
    }
    
    // إضافة الأعمدة المفقودة إذا لزم الأمر
    try {
        $pdo->query("SELECT show_whatsapp FROM contact LIMIT 1");
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE contact ADD COLUMN show_whatsapp BOOLEAN DEFAULT TRUE");
        $pdo->exec("ALTER TABLE contact ADD COLUMN show_phone BOOLEAN DEFAULT TRUE");
        $pdo->exec("ALTER TABLE contact ADD COLUMN show_email BOOLEAN DEFAULT TRUE");
    }
    
    // إدخال البيانات الافتراضية
    $check = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
    if ($check == 0) {
        $pdo->exec("INSERT INTO settings (site_name, logo_text) VALUES ('راية الابتكار للمطابخ', 'راية الابتكار للمطابخ')");
    }
    
    $check = $pdo->query("SELECT COUNT(*) FROM home_content")->fetchColumn();
    if ($check == 0) {
        $pdo->exec("INSERT INTO home_content (badge_text, quality_title, quality_description) VALUES 
            ('أهلاً بكم في راية الابتكار', 'مصمم للاستخدام اليومي، يدوم لأطول فترة.', 'من المعاينة إلى التركيب، نصنع مطابخ تعمل بشكل أفضل كل يوم.')");
    }
    
    $check = $pdo->query("SELECT COUNT(*) FROM workflow")->fetchColumn();
    if ($check == 0) {
        $pdo->exec("INSERT INTO workflow (step_number, title, description, icon, sort_order) VALUES
            (1, 'المعاينة', 'نبدأ بمعاينة دقيقة وأخذ المقاسات', 'fa-ruler-combined', 1),
            (2, 'التصميم', 'تصميم عملي يناسب مساحتك', 'fa-pencil-ruler', 2),
            (3, 'التركيب', 'فريق محترف وتركيب دقيق', 'fa-tools', 3),
            (4, 'التسليم', 'تسليم نظيف وفي الموعد', 'fa-check-double', 4)");
    }
    
    $check = $pdo->query("SELECT COUNT(*) FROM location")->fetchColumn();
    if ($check == 0) {
        // رابط الخريطة الجديد
        $map_url = "https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3627.89340533378!2d46.73827618500236!3d24.59287468418083!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMjTCsDM1JzM0LjQiTiA0NsKwNDQnMDkuOSJF!5e0!3m2!1sar!2seg!4v1771645480045!5m2!1sar!2seg";
        
        $pdo->exec("INSERT INTO location (city, district, address, working_hours, map_url) VALUES
            ('الرياض', 'حي العليا', 'شارع التخصصي، تقاطع طريق الملك فهد', '٩:٣٠ صباحاً – ١٠:٠٠ مساءً', '$map_url')");
    }
    
    $check = $pdo->query("SELECT COUNT(*) FROM contact")->fetchColumn();
    if ($check == 0) {
        $pdo->exec("INSERT INTO contact (whatsapp, phone, email, show_whatsapp, show_phone, show_email) VALUES
            ('+966 55 123 4567', '011 456 7890', 'info@raya-innovation.sa', TRUE, TRUE, TRUE)");
    }
    
    $check = $pdo->query("SELECT COUNT(*) FROM social_media")->fetchColumn();
    if ($check == 0) {
        $pdo->exec("INSERT INTO social_media (platform, icon, link, sort_order, is_active) VALUES
            ('فيسبوك', 'fa-facebook', '#', 1, FALSE),
            ('انستغرام', 'fa-instagram', '#', 2, FALSE),
            ('تويتر', 'fa-twitter', '#', 3, FALSE),
            ('سناب شات', 'fa-snapchat', '#', 4, FALSE)");
    }
    
} catch(PDOException $e) {
    die("خطأ في قاعدة البيانات: " . $e->getMessage());
}

// ========== جلب البيانات ==========
$settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
$home = $pdo->query("SELECT * FROM home_content WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
$kitchens = $pdo->query("SELECT * FROM gallery WHERE type = 'kitchen' ORDER BY sort_order")->fetchAll();
$dressing = $pdo->query("SELECT * FROM gallery WHERE type = 'dressing' ORDER BY sort_order")->fetchAll();
$offers = $pdo->query("SELECT * FROM offers ORDER BY sort_order")->fetchAll();
$videos = $pdo->query("SELECT * FROM videos ORDER BY sort_order")->fetchAll();
$testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order")->fetchAll();
$location = $pdo->query("SELECT * FROM location WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
$contact = $pdo->query("SELECT * FROM contact WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
$social_media = $pdo->query("SELECT * FROM social_media WHERE is_active = TRUE ORDER BY sort_order")->fetchAll();
$workflow = $pdo->query("SELECT * FROM workflow ORDER BY sort_order")->fetchAll();

// التأكد من وجود قيم للحقول
if (!$contact) {
    $contact = [
        'whatsapp' => '+966 55 123 4567',
        'phone' => '011 456 7890',
        'email' => 'info@raya-innovation.sa',
        'show_whatsapp' => 1,
        'show_phone' => 1,
        'show_email' => 1
    ];
}

// ========== معالجة الإدارة (عند إضافة ?admin في الرابط) ==========
$is_admin = isset($_GET['admin']);

if ($is_admin && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // إنشاء مجلد uploads إذا لم يكن موجوداً
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }
    
    // تحديث المحتوى الرئيسي
    if (isset($_POST['update_home'])) {
        $stmt = $pdo->prepare("UPDATE home_content SET 
            badge_text = ?, experience_years = ?, projects_count = ?, 
            happy_clients = ?, branches_count = ?, quality_title = ?, quality_description = ? WHERE id = 1");
        $stmt->execute([
            $_POST['badge_text'],
            $_POST['experience_years'],
            $_POST['projects_count'],
            $_POST['happy_clients'],
            $_POST['branches_count'],
            $_POST['quality_title'],
            $_POST['quality_description']
        ]);
        $message = "✅ تم تحديث المحتوى الرئيسي";
    }
    
    // إضافة صورة
    if (isset($_POST['add_image'])) {
        $type = $_POST['type'];
        $title = $_POST['title'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $filename = time() . "_" . uniqid() . "." . $ext;
            $target = "uploads/" . $filename;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
                $stmt = $pdo->prepare("INSERT INTO gallery (type, title, image_path, sort_order) VALUES (?, ?, ?, ?)");
                $stmt->execute([$type, $title, $target, $_POST['sort_order']]);
                $message = "✅ تم إضافة الصورة";
            }
        }
    }
    
    // إضافة فيديو
    if (isset($_POST['add_video'])) {
        $stmt = $pdo->prepare("INSERT INTO videos (title, youtube_url, sort_order) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['title'], $_POST['youtube_url'], $_POST['sort_order']]);
        $message = "✅ تم إضافة الفيديو";
    }
    
    // إضافة رأي
    if (isset($_POST['add_testimonial'])) {
        $stmt = $pdo->prepare("INSERT INTO testimonials (client_name, client_city, content, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['client_name'], $_POST['client_city'], $_POST['content'], $_POST['sort_order']]);
        $message = "✅ تم إضافة الرأي";
    }
    
    // إضافة عرض
    if (isset($_POST['add_offer'])) {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $filename = time() . "_" . uniqid() . "." . $ext;
            $target = "uploads/" . $filename;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
                $stmt = $pdo->prepare("INSERT INTO offers (title, image_path, description, sort_order) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['title'], $target, $_POST['description'], $_POST['sort_order']]);
                $message = "✅ تم إضافة العرض";
            }
        }
    }
    
    // تحديث معلومات الاتصال
    if (isset($_POST['update_contact'])) {
        $stmt = $pdo->prepare("UPDATE contact SET 
            whatsapp = ?, phone = ?, email = ?,
            show_whatsapp = ?, show_phone = ?, show_email = ? WHERE id = 1");
        $stmt->execute([
            $_POST['whatsapp'],
            $_POST['phone'],
            $_POST['email'],
            isset($_POST['show_whatsapp']) ? 1 : 0,
            isset($_POST['show_phone']) ? 1 : 0,
            isset($_POST['show_email']) ? 1 : 0
        ]);
        $message = "✅ تم تحديث معلومات الاتصال";
    }
    
    // إضافة/تحديث وسائل التواصل
    if (isset($_POST['add_social'])) {
        $stmt = $pdo->prepare("INSERT INTO social_media (platform, icon, link, sort_order, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['platform'],
            $_POST['icon'],
            $_POST['link'],
            $_POST['sort_order'],
            isset($_POST['is_active']) ? 1 : 0
        ]);
        $message = "✅ تم إضافة وسيلة التواصل";
    }
    
    if (isset($_POST['update_social'])) {
        foreach ($_POST['social'] as $id => $data) {
            $stmt = $pdo->prepare("UPDATE social_media SET platform = ?, link = ?, sort_order = ?, is_active = ? WHERE id = ?");
            $stmt->execute([
                $data['platform'],
                $data['link'],
                $data['sort_order'],
                isset($data['is_active']) ? 1 : 0,
                $id
            ]);
        }
        $message = "✅ تم تحديث وسائل التواصل";
    }
    
    // تحديث الموقع
    if (isset($_POST['update_location'])) {
        $stmt = $pdo->prepare("UPDATE location SET city=?, district=?, address=?, working_hours=?, map_url=? WHERE id=1");
        $stmt->execute([
            $_POST['city'], 
            $_POST['district'], 
            $_POST['address'], 
            $_POST['working_hours'], 
            $_POST['map_url']
        ]);
        $message = "✅ تم تحديث الموقع";
    }
    
    // حذف عنصر
    if (isset($_POST['delete'])) {
        $table = $_POST['table'];
        $id = $_POST['id'];
        
        if ($table == 'gallery' || $table == 'offers') {
            $stmt = $pdo->prepare("SELECT image_path FROM $table WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            if ($item && file_exists($item['image_path'])) {
                unlink($item['image_path']);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        $message = "✅ تم الحذف";
        
        // إعادة تحميل الصفحة
        header("Location: ?admin");
        exit;
    }
    
    // تحديث الترتيب
    if (isset($_POST['update_order'])) {
        foreach ($_POST['order'] as $id => $order) {
            $stmt = $pdo->prepare("UPDATE {$_POST['table']} SET sort_order = ? WHERE id = ?");
            $stmt->execute([$order, $id]);
        }
        $message = "✅ تم تحديث الترتيب";
    }
}

// ========== إعادة تحميل البيانات بعد أي تعديل ==========
if ($is_admin && isset($message)) {
    $settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
    $home = $pdo->query("SELECT * FROM home_content WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
    $kitchens = $pdo->query("SELECT * FROM gallery WHERE type = 'kitchen' ORDER BY sort_order")->fetchAll();
    $dressing = $pdo->query("SELECT * FROM gallery WHERE type = 'dressing' ORDER BY sort_order")->fetchAll();
    $offers = $pdo->query("SELECT * FROM offers ORDER BY sort_order")->fetchAll();
    $videos = $pdo->query("SELECT * FROM videos ORDER BY sort_order")->fetchAll();
    $testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order")->fetchAll();
    $location = $pdo->query("SELECT * FROM location WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
    $contact = $pdo->query("SELECT * FROM contact WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
    $social_media = $pdo->query("SELECT * FROM social_media WHERE is_active = TRUE ORDER BY sort_order")->fetchAll();
    $workflow = $pdo->query("SELECT * FROM workflow ORDER BY sort_order")->fetchAll();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title>راية الابتكار للمطابخ</title>
  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- جوجل فونت -->
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body, html {
      height: 100%;
      width: 100%;
      background: #0a1c28;
      font-family: 'Tajawal', sans-serif;
      direction: rtl;
    }

    .mobile-container {
      max-width: 420px;
      width: 100%;
      height: 100vh;
      margin: 0 auto;
      background: #ffffff;
      box-shadow: 0 0 40px rgba(0,0,0,0.2);
      border-radius: 36px 36px 0 0;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      position: relative;
    }

    /* زر الواتساب العائم */
    .whatsapp-float {
      position: fixed;
      bottom: 20px;
      left: 20px;
      width: 60px;
      height: 60px;
      background-color: #25d366;
      color: white;
      border-radius: 50px;
      text-align: center;
      font-size: 30px;
      box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: 0.3s;
      border: 2px solid white;
    }
    
    .whatsapp-float:hover {
      transform: scale(1.1);
      background-color: #20ba5c;
    }

    /* الشريط العلوي */
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      background: linear-gradient(145deg, #0a3143, #041c26);
      color: white;
    }

    .logo-area {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo-icon {
      font-size: 1.8rem;
      color: #d4af37;
    }

    .app-title {
      font-weight: 800;
      font-size: 1.1rem;
      letter-spacing: 0.3px;
    }

    .hamburger-btn {
      background: none;
      border: none;
      color: white;
      font-size: 1.8rem;
      cursor: pointer;
    }

    /* السايدبار */
    .sidebar-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.6);
      visibility: hidden;
      opacity: 0;
      transition: 0.25s ease;
      z-index: 999;
    }
    .sidebar-overlay.active { visibility: visible; opacity: 1; }
    .sidebar {
      position: fixed;
      top: 0;
      right: -280px;
      width: 280px;
      height: 100%;
      background: #0a3143;
      color: white;
      transition: 0.3s ease;
      z-index: 1000;
      padding: 30px 20px;
      box-shadow: -5px 0 25px rgba(0,0,0,0.3);
      display: flex;
      flex-direction: column;
    }
    .sidebar.active { right: 0; }
    .sidebar-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 40px;
      border-bottom: 1px solid #2a5068;
      padding-bottom: 15px;
    }
    .sidebar-header h3 { font-size: 1.5rem; color: #d4af37; }
    .close-sidebar { background: none; border: none; color: white; font-size: 1.7rem; cursor: pointer; }
    .sidebar-menu { list-style: none; flex: 1; }
    .sidebar-menu li { margin-bottom: 25px; }
    .sidebar-menu a {
      color: white; text-decoration: none; font-size: 1.2rem;
      display: flex; align-items: center; gap: 15px; padding: 8px 12px;
      border-radius: 10px; transition: 0.2s;
    }
    .sidebar-menu a i { width: 25px; text-align: center; color: #d4af37; }
    .sidebar-menu a:hover { background: #1e4a62; }
    .sidebar-contact {
      margin-top: 30px; padding-top: 20px; border-top: 1px solid #2a5068;
      font-size: 0.95rem; color: #e0eef7;
    }
    .sidebar-contact p { margin-bottom: 12px; }
    .sidebar-contact i { margin-left: 8px; color: #d4af37; }

    /* المحتوى الرئيسي */
    .main-content {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
      background: #fafcfe;
      scroll-behavior: smooth;
    }

    .section { display: none; }
    .section.active-section { display: block; }

    .section-title {
      font-size: 1.5rem;
      margin-bottom: 20px;
      color: #0a3143;
      border-right: 6px solid #d4af37;
      padding-right: 15px;
      font-weight: 700;
    }

    /* شعار المحل */
    .hero-logo-container {
      background: linear-gradient(145deg, #0a3143, #08303f);
      border-radius: 28px;
      padding: 25px 20px;
      margin-bottom: 25px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      box-shadow: 0 12px 25px rgba(10,49,67,0.2);
      border: 1px solid #d4af37;
    }
    .shop-banner-logo {
      width: 100%;
      max-width: 300px;
      height: auto;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      border: 2px solid #d4af37;
    }
    .logo-caption {
      margin-top: 15px;
      color: white;
      font-size: 1rem;
      background: rgba(212,175,55,0.18);
      padding: 8px 22px;
      border-radius: 50px;
      font-weight: 600;
    }
    
    /* placeholder للصور */
    .image-placeholder {
      width: 100%;
      height: 140px;
      background: linear-gradient(145deg, #f0f0f0, #e0e0e0);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #999;
      font-size: 0.9rem;
      border-bottom: 2px solid #d4af37;
    }

    /* الإحصائيات */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
      margin: 25px 0;
    }
    .stat-card {
      background: white;
      padding: 20px 10px;
      border-radius: 24px;
      text-align: center;
      border: 1px solid #eef5f9;
      box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }
    .stat-number {
      font-size: 2rem;
      font-weight: 800;
      color: #d4af37;
      line-height: 1;
    }
    .stat-label {
      font-size: 0.85rem;
      color: #2c3e50;
      font-weight: 600;
      margin-top: 8px;
    }

    /* خطوات العمل */
    .workflow-steps {
      display: flex;
      flex-direction: column;
      gap: 16px;
      margin: 25px 0;
    }
    .step-item {
      display: flex;
      align-items: center;
      background: white;
      padding: 12px 18px;
      border-radius: 60px;
      border: 1px solid #eaeff4;
      box-shadow: 0 3px 10px rgba(0,0,0,0.02);
    }
    .step-icon {
      background: #d4af37;
      width: 48px;
      height: 48px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #0a3143;
      font-size: 1.4rem;
      font-weight: bold;
      margin-left: 16px;
    }
    .step-text h4 { font-size: 1rem; color: #0a3143; font-weight: 800; }
    .step-text p { font-size: 0.8rem; color: #4a5f6a; }

    /* شبكات الصور */
    .grid-2, .grid-3 {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
    }
    @media (min-width: 400px) { .grid-3 { grid-template-columns: repeat(3, 1fr); } }
    
    .kitchen-card {
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 6px 16px rgba(0,0,0,0.04);
      transition: 0.25s;
      border: 1px solid #edf2f7;
    }
    .kitchen-card img {
      width: 100%;
      height: 140px;
      object-fit: cover;
      display: block;
      border-bottom: 2px solid #d4af37;
    }
    .kitchen-card .card-body {
      padding: 12px 8px;
      font-weight: 600;
      color: #0a3143;
      text-align: center;
      background: white;
    }

    /* الفيديو */
    .video-wrapper {
      position: relative;
      width: 100%;
      aspect-ratio: 16 / 9;
      margin-bottom: 25px;
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 15px 30px rgba(0,0,0,0.1);
      border: 1px solid #d4af37;
    }
    .video-wrapper iframe {
      position: absolute;
      top: 0; right: 0; width: 100%; height: 100%;
      border: none;
    }

    /* الخريطة */
    .map-container {
      border-radius: 24px;
      overflow: hidden;
      margin-top: 15px;
      border: 2px solid #d4af37;
      box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
    .address-box {
      background: white;
      padding: 22px;
      border-radius: 24px;
      margin: 20px 0;
      border: 1px solid #d4af37;
      border-right: 8px solid #d4af37;
    }

    /* آراء العملاء */
    .testimonials {
      margin: 30px 0;
    }
    .testimonial-card {
      background: white;
      padding: 22px;
      border-radius: 28px;
      border: 1px solid #f0e5d4;
      margin-bottom: 16px;
      box-shadow: 0 7px 14px rgba(0,0,0,0.02);
      position: relative;
    }
    .testimonial-card i.fa-quote-right {
      position: absolute;
      bottom: 15px; left: 20px;
      color: #d4af37;
      opacity: 0.2;
      font-size: 2.2rem;
    }
    .client-name {
      font-weight: 800;
      color: #0a3143;
      margin-top: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* الاتصال */
    .contact-info {
      background: white;
      padding: 20px;
      border-radius: 28px;
      border: 1px solid #e6eef5;
    }
    .contact-item {
      display: flex;
      align-items: center;
      gap: 18px;
      padding: 18px 0;
      border-bottom: 1px solid #f0f5fa;
    }
    .contact-item:last-child { border-bottom: none; }
    .contact-icon {
      width: 50px; height: 50px;
      background: rgba(212,175,55,0.1);
      border-radius: 50%;
      display: flex; justify-content: center; align-items: center;
      color: #0a3143; font-size: 1.4rem;
    }

    /* وسائل التواصل الاجتماعي */
    .social-media-section {
      margin-top: 25px;
      padding-top: 15px;
      border-top: 2px solid #f0f5fa;
    }
    .social-media-title {
      font-size: 1.1rem;
      color: #0a3143;
      margin-bottom: 15px;
      font-weight: 700;
    }
    .social-icons {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }
    .social-icon {
      width: 45px;
      height: 45px;
      background: #0a3143;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.3rem;
      text-decoration: none;
      transition: 0.3s;
    }
    .social-icon:hover {
      background: #d4af37;
      transform: translateY(-3px);
    }

    /* الشريط السفلي */
    .bottom-nav {
      display: flex;
      justify-content: space-around;
      align-items: center;
      background: white;
      padding: 10px 6px;
      border-top: 1px solid #d4af37;
      box-shadow: 0 -4px 12px rgba(0,0,0,0.02);
    }
    .nav-item {
      display: flex; flex-direction: column; align-items: center; gap: 5px;
      background: none; border: none;
      font-size: 0.75rem; color: #4a6572; font-weight: 600;
      padding: 8px 12px; border-radius: 20px; transition: 0.2s; cursor: pointer;
    }
    .nav-item i { font-size: 1.5rem; margin-bottom: 3px; }
    .nav-item.active {
      color: #0a3143;
      background: rgba(212,175,55,0.15);
      font-weight: 700;
    }

    .empty-message {
      text-align: center;
      padding: 40px 20px;
      background: #f8fafc;
      border-radius: 24px;
      color: #4a6572;
      font-size: 1rem;
      border: 2px dashed #d4af37;
      margin: 20px 0;
    }
    .empty-message i {
      font-size: 2.5rem;
      color: #d4af37;
      margin-bottom: 15px;
      display: block;
    }

    /* ========== أنماط لوحة الإدارة ========== */
    <?php if ($is_admin): ?>
    .admin-panel {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background: #0a3143;
      color: white;
      padding: 10px;
      text-align: center;
      z-index: 2000;
      border-bottom: 2px solid #d4af37;
    }
    .admin-container {
      max-width: 1200px;
      margin: 80px auto 20px;
      padding: 20px;
      background: #f5f5f5;
      border-radius: 16px;
    }
    .admin-tabs {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }
    .admin-tab-btn {
      padding: 10px 20px;
      background: #0a3143;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
    .admin-tab-btn.active {
      background: #d4af37;
      color: #0a3143;
    }
    .admin-tab { display: none; }
    .admin-tab.active { display: block; }
    .admin-card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .admin-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }
    .admin-item {
      background: #f9f9f9;
      padding: 10px;
      border-radius: 8px;
      text-align: center;
    }
    .admin-item img {
      width: 100%;
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
    }
    .admin-form-group {
      margin-bottom: 15px;
    }
    .admin-form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
    }
    .admin-form-group input,
    .admin-form-group textarea,
    .admin-form-group select {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    .admin-btn {
      background: #0a3143;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
    .admin-btn-danger {
      background: #dc3545;
    }
    .admin-message {
      background: #d4edda;
      color: #155724;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .checkbox-label {
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
    }
    .checkbox-label input[type="checkbox"] {
      width: auto;
    }
    <?php endif; ?>
  </style>
</head>
<body>

<!-- زر الواتساب العائم -->
<?php if (!empty($contact['whatsapp']) && isset($contact['show_whatsapp']) && $contact['show_whatsapp']): ?>
<a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $contact['whatsapp']); ?>" class="whatsapp-float" target="_blank">
  <i class="fab fa-whatsapp"></i>
</a>
<?php endif; ?>

<?php if ($is_admin): ?>
<!-- لوحة الإدارة -->
<div class="admin-panel">
  <i class="fas fa-crown" style="color: #d4af37;"></i> 
  وضع الإدارة - راية الابتكار للمطابخ
  <a href="?" style="color: white; margin-right: 20px;">عرض الموقع</a>
</div>

<div class="admin-container">
  <?php if (isset($message)): ?>
  <div class="admin-message"><?php echo $message; ?></div>
  <?php endif; ?>
  
  <div class="admin-tabs">
    <button class="admin-tab-btn active" onclick="showAdminTab('home')">الرئيسية</button>
    <button class="admin-tab-btn" onclick="showAdminTab('kitchens')">المطابخ</button>
    <button class="admin-tab-btn" onclick="showAdminTab('dressing')">غرف dressing</button>
    <button class="admin-tab-btn" onclick="showAdminTab('offers')">العروض</button>
    <button class="admin-tab-btn" onclick="showAdminTab('videos')">الفيديوهات</button>
    <button class="admin-tab-btn" onclick="showAdminTab('testimonials')">آراء العملاء</button>
    <button class="admin-tab-btn" onclick="showAdminTab('location')">الموقع</button>
    <button class="admin-tab-btn" onclick="showAdminTab('contact')">الاتصال</button>
    <button class="admin-tab-btn" onclick="showAdminTab('social')">وسائل التواصل</button>
  </div>
  
  <!-- تبويب الرئيسية -->
  <div id="admin-tab-home" class="admin-tab active">
    <div class="admin-card">
      <h3>تعديل المحتوى الرئيسي</h3>
      <form method="POST">
        <div class="admin-form-group">
          <label>نص الشعار</label>
          <input type="text" name="badge_text" value="<?php echo htmlspecialchars($home['badge_text']); ?>">
        </div>
        <div class="admin-grid" style="grid-template-columns: repeat(4,1fr);">
          <div class="admin-form-group">
            <label>سنوات الخبرة</label>
            <input type="number" name="experience_years" value="<?php echo $home['experience_years']; ?>">
          </div>
          <div class="admin-form-group">
            <label>المشاريع</label>
            <input type="number" name="projects_count" value="<?php echo $home['projects_count']; ?>">
          </div>
          <div class="admin-form-group">
            <label>العملاء %</label>
            <input type="number" name="happy_clients" value="<?php echo $home['happy_clients']; ?>">
          </div>
          <div class="admin-form-group">
            <label>الفروع</label>
            <input type="number" name="branches_count" value="<?php echo $home['branches_count']; ?>">
          </div>
        </div>
        <div class="admin-form-group">
          <label>عنوان الجودة</label>
          <input type="text" name="quality_title" value="<?php echo htmlspecialchars($home['quality_title']); ?>">
        </div>
        <div class="admin-form-group">
          <label>وصف الجودة</label>
          <textarea name="quality_description" rows="3"><?php echo htmlspecialchars($home['quality_description']); ?></textarea>
        </div>
        <button type="submit" name="update_home" class="admin-btn">حفظ التغييرات</button>
      </form>
    </div>
  </div>
  
  <!-- تبويب المطابخ -->
  <div id="admin-tab-kitchens" class="admin-tab">
    <div class="admin-card">
      <h3>إضافة مطبخ جديد</h3>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="type" value="kitchen">
        <div class="admin-grid" style="grid-template-columns: repeat(3,1fr);">
          <div class="admin-form-group">
            <label>العنوان</label>
            <input type="text" name="title" required>
          </div>
          <div class="admin-form-group">
            <label>الصورة</label>
            <input type="file" name="image" accept="image/*" required>
          </div>
          <div class="admin-form-group">
            <label>الترتيب</label>
            <input type="number" name="sort_order" value="0">
          </div>
        </div>
        <button type="submit" name="add_image" class="admin-btn">إضافة</button>
      </form>
    </div>
    
    <div class="admin-card">
      <h3>المطابخ الحالية</h3>
      <form method="POST">
        <input type="hidden" name="table" value="gallery">
        <div class="admin-grid">
          <?php if (empty($kitchens)): ?>
            <p>لا توجد مطابخ مضافة بعد</p>
          <?php else: ?>
            <?php foreach ($kitchens as $item): ?>
            <div class="admin-item">
              <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['title']; ?>">
              <h4><?php echo $item['title']; ?></h4>
              <input type="number" name="order[<?php echo $item['id']; ?>]" value="<?php echo $item['sort_order']; ?>" style="width:60px;">
              <button type="submit" name="delete" value="1" class="admin-btn admin-btn-danger" style="margin-top:5px;" 
                      onclick="if(confirm('حذف؟')){ this.form.action='?admin'; this.form.innerHTML+='<input type=\'hidden\' name=\'id\' value=\'<?php echo $item['id']; ?>\'>'; return true; } return false;">حذف</button>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <button type="submit" name="update_order" class="admin-btn" style="margin-top:10px;">تحديث الترتيب</button>
      </form>
    </div>
  </div>
  
  <!-- تبويب غرف dressing -->
  <div id="admin-tab-dressing" class="admin-tab">
    <div class="admin-card">
      <h3>إضافة غرفة dressing جديدة</h3>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="type" value="dressing">
        <div class="admin-grid" style="grid-template-columns: repeat(3,1fr);">
          <div class="admin-form-group">
            <label>العنوان</label>
            <input type="text" name="title" required>
          </div>
          <div class="admin-form-group">
            <label>الصورة</label>
            <input type="file" name="image" accept="image/*" required>
          </div>
          <div class="admin-form-group">
            <label>الترتيب</label>
            <input type="number" name="sort_order" value="0">
          </div>
        </div>
        <button type="submit" name="add_image" class="admin-btn">إضافة</button>
      </form>
    </div>
    
    <div class="admin-card">
      <h3>الغرف الحالية</h3>
      <form method="POST">
        <input type="hidden" name="table" value="gallery">
        <div class="admin-grid">
          <?php if (empty($dressing)): ?>
            <p>لا توجد غرف dressing مضافة بعد</p>
          <?php else: ?>
            <?php foreach ($dressing as $item): ?>
            <div class="admin-item">
              <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['title']; ?>">
              <h4><?php echo $item['title']; ?></h4>
              <input type="number" name="order[<?php echo $item['id']; ?>]" value="<?php echo $item['sort_order']; ?>" style="width:60px;">
              <button type="submit" name="delete" value="1" class="admin-btn admin-btn-danger" style="margin-top:5px;" 
                      onclick="if(confirm('حذف؟')){ this.form.action='?admin'; this.form.innerHTML+='<input type=\'hidden\' name=\'id\' value=\'<?php echo $item['id']; ?>\'>'; return true; } return false;">حذف</button>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <button type="submit" name="update_order" class="admin-btn" style="margin-top:10px;">تحديث الترتيب</button>
      </form>
    </div>
  </div>
  
  <!-- تبويب العروض -->
  <div id="admin-tab-offers" class="admin-tab">
    <div class="admin-card">
      <h3>إضافة عرض حصري</h3>
      <form method="POST" enctype="multipart/form-data">
        <div class="admin-grid" style="grid-template-columns: repeat(3,1fr);">
          <div class="admin-form-group">
            <label>العنوان</label>
            <input type="text" name="title" required>
          </div>
          <div class="admin-form-group">
            <label>الصورة</label>
            <input type="file" name="image" accept="image/*" required>
          </div>
          <div class="admin-form-group">
            <label>الترتيب</label>
            <input type="number" name="sort_order" value="0">
          </div>
        </div>
        <div class="admin-form-group">
          <label>الوصف</label>
          <input type="text" name="description">
        </div>
        <button type="submit" name="add_offer" class="admin-btn">إضافة</button>
      </form>
    </div>
    
    <div class="admin-card">
      <h3>العروض الحالية</h3>
      <form method="POST">
        <input type="hidden" name="table" value="offers">
        <div class="admin-grid">
          <?php if (empty($offers)): ?>
            <p>لا توجد عروض مضافة بعد</p>
          <?php else: ?>
            <?php foreach ($offers as $item): ?>
            <div class="admin-item">
              <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['title']; ?>">
              <h4><?php echo $item['title']; ?></h4>
              <input type="number" name="order[<?php echo $item['id']; ?>]" value="<?php echo $item['sort_order']; ?>" style="width:60px;">
              <button type="submit" name="delete" value="1" class="admin-btn admin-btn-danger" style="margin-top:5px;" 
                      onclick="if(confirm('حذف؟')){ this.form.action='?admin'; this.form.innerHTML+='<input type=\'hidden\' name=\'id\' value=\'<?php echo $item['id']; ?>\'>'; return true; } return false;">حذف</button>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <button type="submit" name="update_order" class="admin-btn" style="margin-top:10px;">تحديث الترتيب</button>
      </form>
    </div>
  </div>
  
  <!-- تبويب الفيديوهات -->
  <div id="admin-tab-videos" class="admin-tab">
    <div class="admin-card">
      <h3>إضافة فيديو</h3>
      <form method="POST">
        <div class="admin-grid" style="grid-template-columns: repeat(3,1fr);">
          <div class="admin-form-group">
            <label>العنوان</label>
            <input type="text" name="title" required>
          </div>
          <div class="admin-form-group">
            <label>رابط YouTube</label>
            <input type="url" name="youtube_url" required>
          </div>
          <div class="admin-form-group">
            <label>الترتيب</label>
            <input type="number" name="sort_order" value="0">
          </div>
        </div>
        <button type="submit" name="add_video" class="admin-btn">إضافة</button>
      </form>
    </div>
    
    <div class="admin-card">
      <h3>الفيديوهات الحالية</h3>
      <form method="POST">
        <input type="hidden" name="table" value="videos">
        <div class="admin-grid">
          <?php if (empty($videos)): ?>
            <p>لا توجد فيديوهات مضافة بعد</p>
          <?php else: ?>
            <?php foreach ($videos as $item): ?>
            <div class="admin-item">
              <i class="fas fa-video" style="font-size: 3rem; color: #d4af37; margin: 20px;"></i>
              <h4><?php echo $item['title']; ?></h4>
              <input type="number" name="order[<?php echo $item['id']; ?>]" value="<?php echo $item['sort_order']; ?>" style="width:60px;">
              <button type="submit" name="delete" value="1" class="admin-btn admin-btn-danger" style="margin-top:5px;" 
                      onclick="if(confirm('حذف؟')){ this.form.action='?admin'; this.form.innerHTML+='<input type=\'hidden\' name=\'id\' value=\'<?php echo $item['id']; ?>\'>'; return true; } return false;">حذف</button>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <button type="submit" name="update_order" class="admin-btn" style="margin-top:10px;">تحديث الترتيب</button>
      </form>
    </div>
  </div>
  
  <!-- تبويب آراء العملاء -->
  <div id="admin-tab-testimonials" class="admin-tab">
    <div class="admin-card">
      <h3>إضافة رأي عميل</h3>
      <form method="POST">
        <div class="admin-grid" style="grid-template-columns: repeat(3,1fr);">
          <div class="admin-form-group">
            <label>الاسم</label>
            <input type="text" name="client_name" required>
          </div>
          <div class="admin-form-group">
            <label>المدينة</label>
            <input type="text" name="client_city" required>
          </div>
          <div class="admin-form-group">
            <label>الترتيب</label>
            <input type="number" name="sort_order" value="0">
          </div>
        </div>
        <div class="admin-form-group">
          <label>نص الرأي</label>
          <textarea name="content" rows="3" required></textarea>
        </div>
        <button type="submit" name="add_testimonial" class="admin-btn">إضافة</button>
      </form>
    </div>
    
    <div class="admin-card">
      <h3>آراء العملاء الحالية</h3>
      <form method="POST">
        <input type="hidden" name="table" value="testimonials">
        <div class="admin-grid">
          <?php if (empty($testimonials)): ?>
            <p>لا توجد آراء مضافة بعد</p>
          <?php else: ?>
            <?php foreach ($testimonials as $item): ?>
            <div class="admin-item">
              <i class="fas fa-user-circle" style="font-size: 2rem; color: #0a3143;"></i>
              <h4><?php echo $item['client_name']; ?></h4>
              <p><?php echo $item['client_city']; ?></p>
              <input type="number" name="order[<?php echo $item['id']; ?>]" value="<?php echo $item['sort_order']; ?>" style="width:60px;">
              <button type="submit" name="delete" value="1" class="admin-btn admin-btn-danger" style="margin-top:5px;" 
                      onclick="if(confirm('حذف؟')){ this.form.action='?admin'; this.form.innerHTML+='<input type=\'hidden\' name=\'id\' value=\'<?php echo $item['id']; ?>\'>'; return true; } return false;">حذف</button>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <button type="submit" name="update_order" class="admin-btn" style="margin-top:10px;">تحديث الترتيب</button>
      </form>
    </div>
  </div>
  
  <!-- تبويب الموقع -->
  <div id="admin-tab-location" class="admin-tab">
    <div class="admin-card">
      <h3>تعديل معلومات الموقع</h3>
      <form method="POST">
        <div class="admin-grid" style="grid-template-columns: repeat(2,1fr);">
          <div class="admin-form-group">
            <label>المدينة</label>
            <input type="text" name="city" value="<?php echo htmlspecialchars($location['city']); ?>">
          </div>
          <div class="admin-form-group">
            <label>الحي</label>
            <input type="text" name="district" value="<?php echo htmlspecialchars($location['district']); ?>">
          </div>
          <div class="admin-form-group">
            <label>العنوان</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($location['address']); ?>">
          </div>
          <div class="admin-form-group">
            <label>ساعات العمل</label>
            <input type="text" name="working_hours" value="<?php echo htmlspecialchars($location['working_hours']); ?>">
          </div>
          <div class="admin-form-group">
            <label>رابط الخريطة</label>
            <input type="text" name="map_url" value="<?php echo htmlspecialchars($location['map_url']); ?>">
          </div>
        </div>
        <button type="submit" name="update_location" class="admin-btn">حفظ التغييرات</button>
      </form>
    </div>
  </div>
  
  <!-- تبويب الاتصال -->
  <div id="admin-tab-contact" class="admin-tab">
    <div class="admin-card">
      <h3>تعديل معلومات الاتصال</h3>
      <form method="POST">
        <div class="admin-grid" style="grid-template-columns: repeat(2,1fr);">
          <div class="admin-form-group">
            <label>رقم الواتساب</label>
            <input type="text" name="whatsapp" value="<?php echo htmlspecialchars($contact['whatsapp']); ?>">
          </div>
          <div class="admin-form-group checkbox-label">
            <input type="checkbox" name="show_whatsapp" <?php echo (isset($contact['show_whatsapp']) && $contact['show_whatsapp']) ? 'checked' : ''; ?>> إظهار الواتساب
          </div>
          <div class="admin-form-group">
            <label>رقم الهاتف</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($contact['phone']); ?>">
          </div>
          <div class="admin-form-group checkbox-label">
            <input type="checkbox" name="show_phone" <?php echo (isset($contact['show_phone']) && $contact['show_phone']) ? 'checked' : ''; ?>> إظهار الهاتف
          </div>
          <div class="admin-form-group">
            <label>البريد الإلكتروني</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($contact['email']); ?>">
          </div>
          <div class="admin-form-group checkbox-label">
            <input type="checkbox" name="show_email" <?php echo (isset($contact['show_email']) && $contact['show_email']) ? 'checked' : ''; ?>> إظهار البريد
          </div>
        </div>
        <button type="submit" name="update_contact" class="admin-btn">حفظ التغييرات</button>
      </form>
    </div>
  </div>
  
  <!-- تبويب وسائل التواصل -->
  <div id="admin-tab-social" class="admin-tab">
    <div class="admin-card">
      <h3>إضافة وسيلة تواصل جديدة</h3>
      <form method="POST">
        <div class="admin-grid" style="grid-template-columns: repeat(3,1fr);">
          <div class="admin-form-group">
            <label>اسم المنصة</label>
            <input type="text" name="platform" required placeholder="فيسبوك">
          </div>
          <div class="admin-form-group">
            <label>أيقونة Font Awesome</label>
            <input type="text" name="icon" required placeholder="fa-facebook">
          </div>
          <div class="admin-form-group">
            <label>الرابط</label>
            <input type="url" name="link" required>
          </div>
          <div class="admin-form-group">
            <label>الترتيب</label>
            <input type="number" name="sort_order" value="0">
          </div>
          <div class="admin-form-group checkbox-label">
            <input type="checkbox" name="is_active" checked> فعال
          </div>
        </div>
        <button type="submit" name="add_social" class="admin-btn">إضافة</button>
      </form>
    </div>
    
    <div class="admin-card">
      <h3>وسائل التواصل الحالية</h3>
      <?php 
      $all_social = $pdo->query("SELECT * FROM social_media ORDER BY sort_order")->fetchAll();
      if (empty($all_social)): ?>
        <p>لا توجد وسائل تواصل مضافة</p>
      <?php else: ?>
      <form method="POST">
        <div class="admin-grid">
          <?php foreach ($all_social as $item): ?>
          <div class="admin-item">
            <i class="fab <?php echo $item['icon']; ?>" style="font-size: 2rem; color: #0a3143;"></i>
            <h4><?php echo $item['platform']; ?></h4>
            <div class="admin-form-group">
              <label>المنصة</label>
              <input type="text" name="social[<?php echo $item['id']; ?>][platform]" value="<?php echo $item['platform']; ?>">
            </div>
            <div class="admin-form-group">
              <label>الرابط</label>
              <input type="url" name="social[<?php echo $item['id']; ?>][link]" value="<?php echo $item['link']; ?>">
            </div>
            <div class="admin-form-group">
              <label>الترتيب</label>
              <input type="number" name="social[<?php echo $item['id']; ?>][sort_order]" value="<?php echo $item['sort_order']; ?>">
            </div>
            <div class="admin-form-group checkbox-label">
              <input type="checkbox" name="social[<?php echo $item['id']; ?>][is_active]" <?php echo $item['is_active'] ? 'checked' : ''; ?>> فعال
            </div>
            <button type="submit" name="delete" value="1" class="admin-btn admin-btn-danger" style="margin-top:5px;" 
                    onclick="if(confirm('حذف؟')){ this.form.action='?admin'; this.form.innerHTML+='<input type=\'hidden\' name=\'table\' value=\'social_media\'><input type=\'hidden\' name=\'id\' value=\'<?php echo $item['id']; ?>\'>'; return true; } return false;">حذف</button>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="submit" name="update_social" class="admin-btn" style="margin-top:20px;">تحديث جميع وسائل التواصل</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
function showAdminTab(tabName) {
  document.querySelectorAll('.admin-tab').forEach(tab => tab.classList.remove('active'));
  document.querySelectorAll('.admin-tab-btn').forEach(btn => btn.classList.remove('active'));
  document.getElementById('admin-tab-' + tabName).classList.add('active');
  event.target.classList.add('active');
}
</script>
<?php endif; ?>

<div class="mobile-container">
  <!-- الشريط العلوي -->
  <div class="top-bar">
    <div class="logo-area">
      <i class="fas <?php echo $settings['logo_icon']; ?> logo-icon"></i>
      <span class="app-title"><?php echo $settings['logo_text']; ?></span>
    </div>
    <button class="hamburger-btn" id="hamburgerBtn"><i class="fas fa-bars"></i></button>
  </div>

  <!-- السايدبار -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <h3><i class="fas fa-crown"></i> <?php echo $settings['logo_text']; ?></h3>
      <button class="close-sidebar" id="closeSidebarBtn"><i class="fas fa-times"></i></button>
    </div>
    <ul class="sidebar-menu">
      <li><a href="#" onclick="showSection('home'); closeSidebar();"><i class="fas fa-home"></i> الرئيسية</a></li>
      <li><a href="#" onclick="showSection('gallery'); closeSidebar();"><i class="fas fa-images"></i> معرض المطابخ</a></li>
      <li><a href="#" onclick="showSection('dressing'); closeSidebar();"><i class="fas fa-tshirt"></i> غرف dressing</a></li>
      <li><a href="#" onclick="showSection('videos'); closeSidebar();"><i class="fas fa-video"></i> فيديوهات</a></li>
      <li><a href="#" onclick="showSection('location'); closeSidebar();"><i class="fas fa-map-marker-alt"></i> موقعنا</a></li>
      <li><a href="#" onclick="showSection('contact'); closeSidebar();"><i class="fas fa-headset"></i> اتصل بنا</a></li>
      <?php if (!$is_admin): ?>
      <li style="margin-top: 50px; border-top: 1px solid #2a5068; padding-top: 20px;">
        <a href="?admin" style="color: #d4af37;"><i class="fas fa-cog"></i> لوحة الإدارة</a>
      </li>
      <?php endif; ?>
    </ul>
    <div class="sidebar-contact">
      <?php if (isset($contact['show_phone']) && $contact['show_phone']): ?>
      <p><i class="fas fa-phone-alt"></i> <?php echo $contact['phone']; ?></p>
      <?php endif; ?>
      <?php if (isset($contact['show_email']) && $contact['show_email']): ?>
      <p><i class="fas fa-envelope"></i> <?php echo $contact['email']; ?></p>
      <?php endif; ?>
      <p><i class="fas fa-map-pin"></i> <?php echo $location['city']; ?> – <?php echo $location['district']; ?></p>
    </div>
  </aside>

  <!-- المحتوى الرئيسي -->
  <div class="main-content" id="mainContent">

    <!-- ===== الرئيسية ===== -->
    <div id="home-section" class="section active-section">
      <!-- شعار المحل -->
      <div class="hero-logo-container">
        <img class="shop-banner-logo" src="https://placehold.co/600x200/0a3143/d4af37?text=<?php echo urlencode($settings['logo_text']); ?>&font=roboto" alt="<?php echo $settings['logo_text']; ?>">
        <span class="logo-caption"><i class="fas fa-check-circle"></i> <?php echo $home['badge_text']; ?></span>
      </div>

      <!-- شعار الجودة -->
      <div style="background: #0a3143; padding: 20px; border-radius: 24px; margin-bottom: 20px; color: white; text-align: center; border: 1px solid #d4af37;">
        <i class="fas fa-gem" style="color: #d4af37; font-size: 2rem; margin-bottom: 8px;"></i>
        <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 6px;"><?php echo $home['quality_title']; ?></h2>
        <p style="color: #e0eef7;"><?php echo $home['quality_description']; ?></p>
      </div>

      <!-- الإحصائيات -->
      <div class="stats-grid">
        <div class="stat-card"><div class="stat-number"><?php echo $home['experience_years']; ?>+</div><div class="stat-label">سنوات خبرة</div></div>
        <div class="stat-card"><div class="stat-number"><?php echo $home['projects_count']; ?></div><div class="stat-label">مشروع مكتمل</div></div>
        <div class="stat-card"><div class="stat-number"><?php echo $home['happy_clients']; ?>%</div><div class="stat-label">عملاء سعداء</div></div>
        <div class="stat-card"><div class="stat-number"><?php echo $home['branches_count']; ?></div><div class="stat-label">فروع لخدمتكم</div></div>
      </div>

      <h3 class="section-title">أحدث تصاميم المطابخ</h3>
      <div class="grid-2">
        <?php 
        $latest_kitchens = array_slice($kitchens, 0, 4);
        if (empty($latest_kitchens)): 
        ?>
        <div class="kitchen-card">
          <div class="image-placeholder">
            <i class="fas fa-image"></i> أضف صورة
          </div>
          <div class="card-body">أضف تصميم مطبخ</div>
        </div>
        <div class="kitchen-card">
          <div class="image-placeholder">
            <i class="fas fa-image"></i> أضف صورة
          </div>
          <div class="card-body">أضف تصميم مطبخ</div>
        </div>
        <div class="kitchen-card">
          <div class="image-placeholder">
            <i class="fas fa-image"></i> أضف صورة
          </div>
          <div class="card-body">أضف تصميم مطبخ</div>
        </div>
        <div class="kitchen-card">
          <div class="image-placeholder">
            <i class="fas fa-image"></i> أضف صورة
          </div>
          <div class="card-body">أضف تصميم مطبخ</div>
        </div>
        <?php else: ?>
          <?php foreach ($latest_kitchens as $item): ?>
          <div class="kitchen-card">
            <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['title']; ?>">
            <div class="card-body"><?php echo $item['title']; ?></div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- العروض الحصرية -->
      <?php if (!empty($offers)): ?>
      <h3 class="section-title" style="margin-top: 25px;"><i class="fas fa-tag"></i> عروض وتصاميم حصرية</h3>
      <div class="grid-2">
        <?php foreach ($offers as $offer): ?>
        <div class="kitchen-card">
          <img src="<?php echo $offer['image_path']; ?>" alt="<?php echo $offer['title']; ?>">
          <div class="card-body"><?php echo $offer['title']; ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- خطوات العمل -->
      <h3 class="section-title" style="margin-top: 25px;">خطوات تنفيذ مطبخك</h3>
      <div class="workflow-steps">
        <?php foreach ($workflow as $step): ?>
        <div class="step-item">
          <div class="step-icon"><i class="fas <?php echo $step['icon']; ?>"></i></div>
          <div class="step-text">
            <h4><?php echo $step['title']; ?></h4>
            <p><?php echo $step['description']; ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- ===== معرض المطابخ ===== -->
    <div id="gallery-section" class="section">
      <h2 class="section-title"><i class="fas fa-camera"></i> معرض مطابخ راية الابتكار</h2>
      <p style="background: #d4af3710; padding: 14px; border-radius: 20px; margin-bottom: 18px;"><i class="fas fa-crown" style="color: #d4af37;"></i> تشكيلة واسعة من أحدث صيحات المطابخ العصرية.</p>
      
      <?php if (empty($kitchens)): ?>
      <div class="empty-message">
        <i class="fas fa-images"></i>
        <p>لا توجد صور في المعرض حالياً</p>
        <p style="font-size:0.9rem; margin-top:10px;">يمكنك إضافة صور من لوحة الإدارة</p>
      </div>
      <?php else: ?>
      <div class="grid-3">
        <?php foreach ($kitchens as $item): ?>
        <div class="kitchen-card">
          <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['title']; ?>">
          <div class="card-body"><?php echo $item['title']; ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- ===== غرف Dressing ===== -->
    <div id="dressing-section" class="section">
      <h2 class="section-title"><i class="fas fa-tshirt"></i> غرف dressing فاخرة</h2>
      
      <?php if (empty($dressing)): ?>
      <div class="empty-message">
        <i class="fas fa-tshirt"></i>
        <p>لا توجد صور لغرف dressing حالياً</p>
        <p style="font-size:0.9rem; margin-top:10px;">يمكنك إضافة صور من لوحة الإدارة</p>
      </div>
      <?php else: ?>
      <div class="grid-2">
        <?php foreach ($dressing as $item): ?>
        <div class="kitchen-card">
          <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['title']; ?>">
          <div class="card-body"><?php echo $item['title']; ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      
      <p style="margin-top: 15px; background: #0a3143; color: white; padding: 14px; border-radius: 50px; text-align: center;">
        <i class="fas fa-star" style="color: #d4af37;"></i> تصميم وتنفيذ حسب الطلب – اتصل بنا للمعاينة
      </p>
    </div>

    <!-- ===== الفيديوهات ===== -->
    <div id="videos-section" class="section">
      <h2 class="section-title"><i class="fas fa-play-circle"></i> جولات في المعرض</h2>
      
      <?php if (empty($videos)): ?>
      <div class="empty-message">
        <i class="fas fa-video"></i>
        <p>لا توجد فيديوهات حالياً</p>
        <p style="font-size:0.9rem; margin-top:10px;">يمكنك إضافة فيديوهات من لوحة الإدارة</p>
      </div>
      <?php else: ?>
        <?php foreach ($videos as $video): 
          $youtube_id = '';
          preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video['youtube_url'], $matches);
          $youtube_id = $matches[1] ?? '';
        ?>
        <div class="video-wrapper">
          <iframe src="https://www.youtube.com/embed/<?php echo $youtube_id; ?>" allowfullscreen></iframe>
        </div>
        <p style="margin-bottom: 20px; font-weight: 700;"><?php echo $video['title']; ?></p>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- ===== الموقع ===== -->
    <div id="location-section" class="section">
      <h2 class="section-title"><i class="fas fa-store"></i> موقع راية الابتكار</h2>
      <div class="address-box">
        <i class="fas fa-map-marked-alt fa-xl" style="color:#d4af37;"></i>
        <span style="font-size: 1.2rem; margin-right: 10px;"><strong><?php echo $location['city']; ?> – <?php echo $location['district']; ?></strong></span>
        <p style="margin-top: 16px;"><i class="fas fa-location-dot"></i> <?php echo $location['address']; ?></p>
        <p style="margin-top: 12px;"><i class="fas fa-clock"></i> ساعات العمل: <?php echo $location['working_hours']; ?></p>
      </div>
      <div class="map-container">
        <iframe src="<?php echo $location['map_url']; ?>" width="100%" height="280" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div>
    </div>

    <!-- ===== اتصل بنا ===== -->
    <div id="contact-section" class="section">
      <h2 class="section-title"><i class="fas fa-headset"></i> تواصل معنا</h2>
      
      <!-- آراء العملاء -->
      <?php if (!empty($testimonials)): ?>
      <div class="testimonials">
        <?php foreach ($testimonials as $testimonial): ?>
        <div class="testimonial-card">
          <p style="font-size: 0.95rem; line-height: 1.6;">"<?php echo $testimonial['content']; ?>"</p>
          <div class="client-name">
            <i class="fas fa-user-circle" style="color: #d4af37;"></i> 
            <?php echo $testimonial['client_name']; ?> – <?php echo $testimonial['client_city']; ?>
          </div>
          <i class="fas fa-quote-right"></i>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- معلومات الاتصال (بدون الموقع الإلكتروني) -->
      <div class="contact-info">
        <?php if (isset($contact['show_whatsapp']) && $contact['show_whatsapp'] && !empty($contact['whatsapp'])): ?>
        <div class="contact-item">
          <div class="contact-icon"><i class="fab fa-whatsapp"></i></div>
          <div>
            <h4>واتساب</h4>
            <p style="font-size: 1.1rem;">
              <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $contact['whatsapp']); ?>" style="color: #0a3143; text-decoration: none;">
                <?php echo $contact['whatsapp']; ?>
              </a>
            </p>
          </div>
        </div>
        <?php endif; ?>
        
        <?php if (isset($contact['show_phone']) && $contact['show_phone'] && !empty($contact['phone'])): ?>
        <div class="contact-item">
          <div class="contact-icon"><i class="fas fa-phone"></i></div>
          <div>
            <h4>هاتف</h4>
            <p><a href="tel:<?php echo $contact['phone']; ?>" style="color: #0a3143; text-decoration: none;"><?php echo $contact['phone']; ?></a></p>
          </div>
        </div>
        <?php endif; ?>
        
        <?php if (isset($contact['show_email']) && $contact['show_email'] && !empty($contact['email'])): ?>
        <div class="contact-item">
          <div class="contact-icon"><i class="fas fa-envelope"></i></div>
          <div>
            <h4>البريد الإلكتروني</h4>
            <p><a href="mailto:<?php echo $contact['email']; ?>" style="color: #0a3143; text-decoration: none;"><?php echo $contact['email']; ?></a></p>
          </div>
        </div>
        <?php endif; ?>
      </div>
      
      <!-- وسائل التواصل الاجتماعي -->
      <?php if (!empty($social_media)): ?>
      <div class="social-media-section">
        <h3 class="social-media-title">تابعنا على</h3>
        <div class="social-icons">
          <?php foreach ($social_media as $social): ?>
          <a href="<?php echo $social['link']; ?>" class="social-icon" target="_blank">
            <i class="fab <?php echo $social['icon']; ?>"></i>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- الشريط السفلي -->
  <div class="bottom-nav">
    <button class="nav-item active" onclick="showSection('home')"><i class="fas fa-home"></i><span>الرئيسية</span></button>
    <button class="nav-item" onclick="showSection('gallery')"><i class="fas fa-images"></i><span>المطابخ</span></button>
    <button class="nav-item" onclick="showSection('dressing')"><i class="fas fa-tshirt"></i><span>dressing</span></button>
    <button class="nav-item" onclick="showSection('videos')"><i class="fas fa-video"></i><span>فيديوهات</span></button>
    <button class="nav-item" onclick="showSection('location')"><i class="fas fa-map-marker-alt"></i><span>الموقع</span></button>
  </div>
</div>

<script>
  // التحكم بالسايدبار
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const hamburger = document.getElementById('hamburgerBtn');
  const closeBtn = document.getElementById('closeSidebarBtn');

  function openSidebar() { sidebar.classList.add('active'); overlay.classList.add('active'); }
  function closeSidebar() { sidebar.classList.remove('active'); overlay.classList.remove('active'); }

  hamburger.addEventListener('click', openSidebar);
  closeBtn.addEventListener('click', closeSidebar);
  overlay.addEventListener('click', closeSidebar);
  window.closeSidebar = closeSidebar;

  // التحكم بالأقسام
  const sections = {
    home: document.getElementById('home-section'),
    gallery: document.getElementById('gallery-section'),
    dressing: document.getElementById('dressing-section'),
    videos: document.getElementById('videos-section'),
    location: document.getElementById('location-section'),
    contact: document.getElementById('contact-section')
  };
  const navButtons = document.querySelectorAll('.nav-item');

  window.showSection = function(sectionId) {
    Object.keys(sections).forEach(id => sections[id].classList.remove('active-section'));
    if (sections[sectionId]) sections[sectionId].classList.add('active-section');
    
    navButtons.forEach((btn, index) => {
      btn.classList.remove('active');
      if (sectionId === 'home' && index === 0) btn.classList.add('active');
      if (sectionId === 'gallery' && index === 1) btn.classList.add('active');
      if (sectionId === 'dressing' && index === 2) btn.classList.add('active');
      if (sectionId === 'videos' && index === 3) btn.classList.add('active');
      if (sectionId === 'location' && index === 4) btn.classList.add('active');
    });
    
    closeSidebar();
  };
  window.onload = function() { showSection('home'); };
</script>

</body>
</html>