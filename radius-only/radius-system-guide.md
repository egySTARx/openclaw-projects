# دليل بناء نظام RADIUS للمبتدئين
## Mageek's Guide to Building a RADIUS System

---

## 📖 المقدمة

بصفتك **Mageek** وبدون خبرة برمجية، يمكنك بناء نظام RADIUS فعال باستخدام أدوات جاهزة (No-Code/Low-Code). هذا الدليل سيشرح لك خطوة بخطوة كيفية:

1. فهم ما تحتاجه لنظام RADIUS
2. اختيار أفضل الأسلوب لاحتياجاتك
3. اختيار الأدوات المناسبة
4. تنفيذ النظام خطوة بخطوة
5. الموارد التعليمية لتعلم المزيد

---

## 🎯 الجزء الأول: فهم ما تحتاجه

### ما هو نظام RADIUS؟

**RADIUS** = **R**emote **A**uthentication **D**ial-In **U**ser **S**ervice

هو معيار قياسي (Standard) يستخدم للتحقق من الهوية والمصادقة في الشبكات، الشاشات الذكية، وأجهزة WiFi.

### مثال على الاستخدامات:

✅ **شاشات ذكية:**
- شاشة Starbucks لا تفتح إلا بعد إدخال اسم المستخدم وكلمة المرور

✅ **نقاط اتصال WiFi:**
- نقطة اتصال برقم هاتفك لتستخدم WiFi المشترك

✅ **أجهزة VPN:**
- التحقق من هويتك قبل فتح VPN للشبكة

✅ **أجهزة الحاسب الآلي:**
- إدارة محاولات الدخول للشبكة الداخلية

---

### متى تحتاج لنظام RADIUS؟

**استخدم RADIUS عندما:**

| ✅ الحالة | توضيح |
|---------|-------|
| تدير أكثر من 5 شاشات ذكية | استخدم RADIUS لمزامنة كلمات المرور |
| لديك شبكة WiFi مشتركة | استخدم RADIUS لتسهيل الدخول |
| تحتاج لسجلات واضحة | RADIUS يسجل كل محاولة دخول |
| تحتاج سياسة أمان قوية | قاعدة البيانات تشفر البيانات بشكل أفضل |

**لا تحتاج RADIUS إذا:**
- لديك فقط 1-2 شاشات (يمكنك استخدام App مباشرة)
- لا تحتاج لتدقيق دخول المستخدمين

---

## 🛠️ الجزء الثاني: اختيار الأسلوب المناسب

لديك 3 خيارات: **No-Code** (بدون برمجة) → **Low-Code** (قليل البرمجة) → **Coding** (برمجة كاملة)

### الخيار 1: No-Code (الأفضل لـ Mageek!)

**التعريف:** استخدام أدوات جاهزة بدون كتابة أي أكواد

**المميزات:**
- ✅ أسهل خيار بالتأكيد
- ✅ لا تحتاج لمعرفة برمجة
- ✅ عادة مجاني (Open Source)
- ✅ دعم مجتمع كبير

**العيوب:**
- ❌ محدودية في التخصيص
- ❌ قد تحتاج لتعلم بعض الأوامر (Bash/CLI)

**الأنظمة المقترحة:**

1. **PAM Authentication Plugin**
   - لإدارة شاشات ذكية (Shenzhen Ninebot, etc.)
   - سهل جداً وبسيط

2. **OpenAirControl** (لا تعمل حالياً - انظر Low-Code)
   - تدير RADIUS + Shelly + Zigbee2MQTT
   - واجهة ويب جميلة جداً

3. **RADIUS Admin Panel (بسيط)**
   - AdminLTE أو Bootstrap Admin Templates
   - جاهز باستخدام PHP + MySQL

---

### الخيار 2: Low-Code (مجموعة أدوات محدودة)

**التعريف:** استخدام PHP/Python + قاعدة بيانات

**المميزات:**
- ✅ يمكن تخصيص بسيط
- ✅ أسرع من No-Code
- ✅ شائع الاستخدام

**العيوب:**
- ❌ تحتاج معرفة بسيطة بـ CLI/Bash
- ❌ تحتاج لإعداد قاعدة بيانات (MySQL)

**الأنظمة المقترحة:**

1. **FreeRADIUS + simple PHP Admin Panel**
   - FreeRADIUS للتحقق
   - PHP Panel لإدارة المستخدمين
   - MySQL للبيانات

2. **NPS (Microsoft Network Policy Server)**
   - مناسب إذا كنت تستخدم Windows
   - واجهة ويب جاهزة

3. **Pi-RADIUS**
   - منصة مبنية على Raspbian
   - واجهة ويب جاهزة

---

### الخيار 3: Coding (برمجة كاملة)

**التعريف:** بناء كل شيء من الصفر

**العيوب:**
- ❌ تحتاج لمعرفة برمجة (Python/PHP/C#)
- ❌ يتطلب وقت طويل لتعلم
- ❌ توجد خيارات أسرع

**⚠️ Mageek: لا أنصحك بهذا الخيار بعد الآن**

---

### 📊 جدول المقارنة

| الخيار | الصعوبة | الوقت | التخصيص |
|--------|--------|-------|---------|
| **No-Code** | ⭐⭐ | 3-5 ساعات | ⭐⭐ |
| **Low-Code** | ⭐⭐⭐ | 5-7 ساعات | ⭐⭐⭐⭐ |
| **Coding** | ⭐⭐⭐⭐⭐ | 2-4 أسابيع | ⭐⭐⭐⭐⭐ |

---

## 🎨 الجزء الثالث: اختيار الأدوات

### الأداة الأساسية: FreeRADIUS

**ما هو FreeRADIUS؟**
- مجاني (Open Source)
- شائع جداً (أكثر من 80% من الشبكات تستخدمه)
- موثوق وقوي

**الوظائف:**
- ✅ تحقق من الهوية (Authentication)
- ✅ إعداد الميزات (Accounting)
- ✅ سجلات مفصلة (Logs)
- ✅ يعمل على Linux/Windows/Mac

---

### الأدوات المساعدة

| الأداة | الاستخدام | الصعوبة |
|--------|----------|--------|
| **MySQL/MariaDB** | قاعدة البيانات | ⭐⭐ |
| **phpMyAdmin** | إدارة قاعدة البيانات | ⭐ |
| **AdminLTE/Bootstrap** | لوحة التحكم | ⭐⭐ |
| **FreeRADIUS Admin** | واجهة إدارة | ⭐⭐ |

---

### خيارات تكامل مع IoT

| الأداة | المهمة | سهولة |
|--------|-------|-------|
| **Shelly** | لوحات التحكم الذكية | ⭐ |
| **Zigbee2MQTT** | التواصل مع أجهزة Zigbee | ⭐⭐⭐ |
| **OpenAirControl** (deprecated) | RADIUS + Shelly + MQTT | ⭐⭐ |

---

## 🚀 الجزء الرابع: التنفيذ خطوة بخطوة

### المهمة 1: إعداد خادم RADIUS

سنستخدم **FreeRADIUS** مع **MySQL** (خيار No-Code/Basic Low-Code)

---

#### الخطوة 1.1: تجهيز الأجهزة
```
✅ جهاز واحد يعمل على Linux (Raspberry Pi 4 مثلاً)
✅ عنوان IP ثابت (192.168.1.50)
✅ اتصال مستمر بالإنترنت
```

---

#### الخطوة 1.2: تثبيت خادم Linux
اختر أحد الخيارات:

**خيار A: Raspberry Pi OS**
```
1. حمّل Raspberry Pi Imager
2. اختر Raspberry Pi 4 Model B
3. اختر بطاقة SD
4. انتظر حتى يكتمل
```

**خيار B: Ubuntu Server**
```
1. حمّل Ubuntu Server 22.04
2. استخدم VirtualBox أو VMware
3. اربط بطاقة شبكة للـ VM
```

---

#### الخطوة 1.3: تثبيت FreeRADIUS
(سأقدم أوامر Bash أساسية)

```bash
# تحديث النظام
sudo apt update && sudo apt upgrade -y

# تثبيت FreeRADIUS و MySQL
sudo apt install freeradius freeradius-mysql -y

# تثبيت phpMyAdmin
sudo apt install phpmyadmin -y
```

---

#### الخطوة 1.4: إعداد قاعدة البيانات
```bash
# تسجيل الدخول كجذر
sudo mysql

# إنشاء قاعدة بيانات
CREATE DATABASE radius;
CREATE USER 'radiususer'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON radius.* TO 'radiususer'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# استيراد هيكل قاعدة البيانات
mysql -u radiususer -p radius < /usr/share/doc/freeradius/examples/sql/mysql/mysql.schema.sql
```

---

#### الخطوة 1.5: إعداد FreeRADIUS
```bash
# تعديل ملف الإعدادات
sudo nano /etc/freeradius/3.0/sites-enabled/default

# ابحث عن السطرين التاليين وقم بتفعيلهما:
# accounting {
#     ...
# }
# auth {
#     ...
# }

# أضف ما يلي في نهاية ملف 'clients.conf':
nano /etc/freeradius/3.0/clients.conf
```

أضف:
```conf
client MyNetwork {
    ipaddr = 192.168.1.0/24
    secret = mysecretkey123
    shortname = home_network
}
```

---

#### الخطوة 1.6: اختبار FreeRADIUS
```bash
# اختبار الاتصال
sudo freeradius -X

# في نافذة منفصلة، استخدم الأمر التالي:
echo -e "User-Password = mysecretkey123\nUser-Name = testuser\nCalling-Station-Id = test" | radtest testuser mysecretkey123 127.0.0.1 0 testing123

# يجب أن ترى رسالة: "Received Access-Accept"
```

---

### المهمة 2: إنشاء لوحة تحكم بسيطة

سنستخدم **AdminLTE** (قالب لوحة تحكم جاهز)

---

#### الخطوة 2.1: تثبيت Apache و PHP
```bash
sudo apt install apache2 php libapache2-mod-php php-mysql -y
```

---

#### الخطوة 2.2: تثبيت AdminLTE
```bash
cd /var/www/html
sudo git clone https://github.com/ColorlibHQ/AdminLTE-2.4.git radius-dashboard
sudo chown -R www-data:www-data radius-dashboard
```

---

#### الخطوة 2.3: إنشاء ملف اتصال قاعدة البيانات
```bash
cd radius-dashboard
nano includes/db.php
```

أضف:
```php
<?php
$servername = "localhost";
$username = "radiususer";
$password = "your_password";
$dbname = "radius";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

---

#### الخطوة 2.4: إنشاء صفحة إدارة المستخدمين
```bash
nano manage_users.php
```

أضف:
```php
<?php
include 'includes/db.php';

// إضافة مستخدم جديد
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // MD5 للتقليد (استخدم Password Hash في المستقبل)
    
    $sql = "INSERT INTO radcheck (username, attribute, op, value)
            VALUES ('$username', 'User-Password', ':=', '$password')";
    
    if ($conn->query($sql) === TRUE) {
        $message = "User added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// الحصول على قائمة المستخدمين
$sql = "SELECT * FROM radcheck";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>RADIUS Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>RADIUS User Management</h2>
        
        <?php if (isset($message)) { ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php } ?>
        
        <div class="card">
            <div class="card-body">
                <h4>Add New User</h4>
                <form method="POST">
                    <div class="form-group">
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-body">
                <h4>Existing Users</h4>
                <table class="table">
                    <tr>
                        <th>Username</th>
                        <th>Attribute</th>
                        <th>Operation</th>
                        <th>Value</th>
                    </tr>
                    <?php while($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['attribute']; ?></td>
                            <td><?php echo $row['op']; ?></td>
                            <td><?php echo $row['value']; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
```

---

#### الخطوة 2.5: الوصول للوحة التحكم
```
1. افتح المتصفح واذهب إلى:
   http://192.168.1.50/radius-dashboard/manage_users.php

2. أضف مستخدمين جديدين

3. استخدم هذه الحسابات في شاشات ذكية أو WiFi:
   - Username: testuser
   - Password: mypassword
```

---

### المهمة 3: تكامل مع Shelly (اختياري)

إذا كان لديك Shelly المنتجات:

```bash
# تثبيت MQTT Broker
sudo apt install mosquitto mosquitto-clients -y

# إعداد Shelly للاتصال بـ MQTT
# اذهب لـ shellies.it في المتصفح

# اختبار اتصال MQTT
mosquitto_sub -h localhost -t "#" -v
```

---

### المهمة 4: استكشاف الأخطاء وإصلاحها

#### مشكلة 1: FreeRADIUS لا يستقبل الاتصالات
```bash
# تحقق من أن الخدمة تعمل
sudo systemctl status freeradius

# إعادة تشغيل الخدمة
sudo systemctl restart freeradius

# عرض السجلات
sudo tail -f /var/log/freeradius/radius.log
```

---

#### مشكلة 2: لا يمكن الاتصال بقاعدة البيانات
```bash
# تحقق من إعدادات SQL في freeradius.conf
nano /etc/freeradius/3.0/sites-enabled/default
```

بحث عن `sql` وفعّل:
```conf
sql {
    database = mysql
    ...
}
```

---

#### مشكلة 3: لا يمكن لشاشات ذكية الاتصال
```bash
# تحقق من ملف clients.conf
cat /etc/freeradius/3.0/clients.conf

# تأكد أن IP الخاص بالشاشة في قائمة العملاء
```

---

## 📚 الجزء الخامس: الموارد التعليمية

### مقالات ومستندات (بالعربية)

| الرابط | الموضوع | الصعوبة |
|--------|--------|--------|
| <https://linuxhint.com/install-and-configure-freeradius-on-ubuntu/> | تثبيت FreeRADIUS | ⭐⭐ |
| <https://linuxize.com/post/setup-freeradius-radius-server/> | تكوين FreeRADIUS | ⭐⭐⭐ |
| <https://openaircontrol.net/guide/> | دليل OpenAirControl | ⭐⭐ |
| <https://github.com/openaircontrol/openaircontrol> | Github - OpenAirControl | ⭐⭐ |

---

### فيديوهات تعليمية (English - subtitles في بعضها)

| الرابط | الموضوع | الطول |
|--------|--------|-------|
| <https://www.youtube.com/watch?v=4rSfMSK6R9w> | FreeRADIUS Tutorial | 10 دقائق |
| <https://www.youtube.com/watch?v=t3OHc8Iq4LE> | RADIUS Server Setup | 15 دقيقة |
| <https://www.youtube.com/watch?v=U12hy1fDW4c> | Managing RADIUS Users | 8 دقائق |

---

### مواقع تعليمية

1. **FreeRADIUS Documentation**
   <https://freeradius.org/doc/>

2. **Linux Journey - RADIUS**
   <https://linuxjourney.com/lesson/radius-authentication>

3. **NetworkLessons - RADIUS**
   <https://networklessons.com/cisco/ccna-200-301-subject-4-4-radius/>

---

### مساباقات مجانية (ممارسة)

1. **Security Labs - RADIUS Simulation**
   <https://securitylabs.whitehatsec.com/learn/labs/>

2. **TryHackMe - RADIUS Basics**
   <https://tryhackme.com/room/radiusbasics/>

---

## 🎓 نصائح لـ Mageek

### نصائح سهلة:

1. **ابدأ بمشروع صغير**
   - 2-3 شاشات ذكية فقط
   - بعد إتقانها، أضف المزيد

2. **استخدم سجلات التحقق**
   - تحقق من السجلات إذا لم يعمل النظام
   - `tail -f /var/log/freeradius/radius.log`

3. **تذكر كلمة المرور**
   - حفظ ملف clients.conf في مكان آمن

4. **ابدأ بخيار No-Code**
   - AdminLTE + MySQL + FreeRADIUS
   - سهل جداً وبسيط

---

### خطط للتطوير:

**المرحلة 1 (الأسبوع الأول):**
- ✅ إعداد Raspberry Pi
- ✅ تثبيت FreeRADIUS
- ✅ إضافة مستخدم واحد
- ✅ اختبار الاتصال

**المرحلة 2 (الأسبوع الثاني):**
- ✅ إنشاء لوحة تحكم بسيطة
- ✅ إضافة 5 مستخدمين
- ✅ ربط مع WiFi

**المرحلة 3 (الأسبوع الثالث):**
- ✅ إضافة سجلات تفصيلية
- ✅ تكامل مع Shelly
- ✅ استخدام phpMyAdmin

---

## ❓ الأسئلة الشائعة

**Q1: هل تحتاج لخادم قوي جداً؟**
A: لا! Raspberry Pi 3 أو 4 كافية تماماً.

**Q2: هل يمكنني استخدام قاعدة بيانات SQLite بدلاً من MySQL؟**
A: نعم! لكن MySQL أفضل للتوسع.

**Q3: هل يمكنني استخدام منصة السحابة؟**
A: نعم! لكنه أغلى. الأفضل خادم خاص.

**Q4: كم من الوقت يحتاج لبناء النظام؟**
A: 3-5 ساعات فقط مع هذه الدليل!

**Q5: هل يمكنني تعديل القوالب؟**
A: نعم! AdminLTE جاهز للتخصيص بسهولة.

---

## 📞 الدعم

إذا واجهت مشكلة:
1. تحقق من السجلات: `tail -f /var/log/freeradius/radius.log`
2. ابحث في Google عن رسالة الخطأ
3. اقرأ دليل FreeRADIUS الرسمي

---

## ✅ خلاصة

كما ترى، **بناء نظام RADIUS للمبتدئين أمر ممكن جداً!**

**الخيار الموصى به لك:**
1. **No-Code Approach**: AdminLTE + MySQL + FreeRADIUS
2. وقت تنفيذ: 3-5 ساعات
3. لا تحتاج لمعرفة برمجة
4. يمكن تخصيصه بالكامل
5. مجاني تماماً

**الخطوات السريعة:**
1. تثبيت Raspberry Pi OS
2. تثبيت FreeRADIUS و MySQL
3. إنشاء لوحة تحكم بسيطة
4. إضافة المستخدمين
5. اختبار النظام

**البداية الآن!** 🚀

---

## 📝 ملخص سريع (Reference Card)

```bash
# تثبيت FreeRADIUS
sudo apt install freeradius freeradius-mysql

# إنشاء قاعدة البيانات
mysql -u root -p

# إضافة مستخدم جديد عبر MySQL
USE radius;
INSERT INTO radcheck (username, attribute, op, value)
VALUES ('mageek', 'User-Password', ':=', 'mypassword');

# اختبار FreeRADIUS
echo -e "User-Password = mypassword\nUser-Name = mageek\nCalling-Station-Id = test" \
  | radtest mageek mypassword 127.0.0.1 0 testing123
```

---

**تاريخ إنشاء الدليل:** 2026-02-02
**المستخدم:** Mageek
**الهدف:** بناء نظام RADIUS للمبتدئين بدون خبرة برمجية
