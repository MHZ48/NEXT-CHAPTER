
<?php 
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
 

?> 
<?php
require_once __DIR__ . '/../vendor/autoload.php'; 
require_once '../connection.php'; // or your DB connection
$imgUrl = isset($_SESSION['profile_img']) && !empty($_SESSION['profile_img'])
    ?  'https://www.w3schools.com/w3images/avatar2.png'
    : 'https://www.w3schools.com/w3images/avatar2.png';
    $user_id = $_SESSION['user_id'] ;
///Graduation-project/All_IMAGES/Profil.png    


use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'dn8gqkmpu',
        'api_key'    => '935617689972222',
        'api_secret' => 'dqzXb7ASR5jELiJNUsyOk0fygEY',
    ],
    'url' => [
        'secure' => true
    ]
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $file = $_FILES['photo']['tmp_name'];

    try {
        $result = $cloudinary->uploadApi()->upload($file, [
            'folder' => 'user_uploads/', // optional
        ]);
        $imageUrl = $result['secure_url'];

        //echo "Image uploaded successfully";

        // Save to DB
        $userId = $_SESSION['user_id'];
        $query = "UPDATE users SET profile_img = '$imageUrl' WHERE id = $userId;
";
        $updateResult = mysqli_query($link, $query);

        if (mysqli_affected_rows($link) == 1) {
            $message = "✅ Image uploaded and saved successfully.";
            $messageType = 'success';
            // Optionally: redirect
            // header("Location: ALL_HTML/home.php");
            // exit();
        } else {
             $message = "⚠️ Could not update user photo in database.";
            $messageType = 'error';

        }
    } catch (Exception $e) {
        $message = "❌ Upload failed: " . htmlspecialchars($e->getMessage());
        $messageType = 'error';

    }
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="./All_CSS/main.css">
    <style>
        /* تنسيقات خاصة بصفحة إعدادات الملف الشخصي */
        :root {
    --color-primary-dark: #0b0804;
    --color-primary-light: #f9c172;
    --color-secondary-dark: #0C0804;
    --color-3rd-dark: #2D241F;
    --color-accent: #c17b36;
    --color-card-text: #E7D6C4;
    --color-card-dark: #bb8d5c;
    --color-white: #fff;
    --prev-img: url(../All_IMAGES/prev.svg);
    --next-img: url(../All_IMAGES/next.svg);
}
input{
    max-width: 90%;
}
        .container-design {
            
            max-width: 900px !important;
            margin-top: 90px;
            margin-bottom: 90px;
            background: var(--color-3rd-dark);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            min-height: 450px !important;
            max-height: 450px !important;
        }

        .nav-link {
            color: var(--color-primary-light) !important;

            cursor: pointer;
        }

        .nav-link.active {
            font-weight: bold;
            color: var(--color-accent) !important;
        }

        .btn-save {
            background-color: #000;
            color: #fff;
            border-radius: 20px;
            padding: 10px 20px;
        }

        .btn-save:hover {
            background-color: #222;
        }

        .content-section {
            display: none;
        }

        /* تغييرات خاصة بحقل البايو */
        .bio-container {
            max-width: 600px;
            margin: auto;
        }

        .bio-label {
            font-weight: bold;
        }

        .char-count {
            font-size: 12px;
            color: #999;
            text-align: right;
        }

        .bio-textarea {
            resize: none;
            height: 120px;
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 10px;
            width: 100%;
        }

        .bio-textarea:focus {
            border-color: #666;
            outline: none;
            box-shadow: none;
        }

        .bio-description {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }

        /* تنسيقات خاصة بشريط قوة كلمة المرور */
        .progress {
            height: 6px;
        }

        /* إزالة أزرار الأسهم من حقل الرقم */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    .continue-btn:hover{
    background-color: var(--color-primary-light) !important;
        color: var(--color-primary-dark) !important;
        box-shadow:  0px 0px 10px var(--color-primary-light);
    }
    .btn-cance{
        border-radius: 1px !important;
        border-color:var(--color-accent) !important;
        border-radius: 5px !important;
        color: var(--color-accent) !important;
    }
       .btn-cance:hover{
        border-radius: 0px !important;
        border-color:var(--color-accent) !important;
        border-radius: 5px !important;
        color: var(--color-primary-dark) !important;
        background-color: var(--color-accent) !important;
                box-shadow:  0px 0px 10px var(--color-primary-light);

    }

    </style>
</head>

<body>
    <!-- Navbar (اختياري إذا كان لديك ملف nav.html) -->
    <div id="navbar-placeholder"></div>
    <script>
        fetch('nav.html')
            .then(response => response.text())
            .then(data => document.getElementById('navbar-placeholder').innerHTML = data);
    </script>

    <div class="container container-design " >
        <div class="d-flex align-items-center mb-4" style="padding-left: 20px;">
            <div class="text-white d-flex align-items-center justify-content-center">
                <img src="https://www.w3schools.com/w3images/avatar2.png" class="rounded-circle"
                    style="width: 50px; height: 50px;" alt="Profile Picture" id="profileImage">
            </div>
            <div class="ms-3">
                <h4 class="mb-0"><?php echo $username; ?></h4>
            </div>
        </div>

        <div class="row" style="min-height: 345px">
            <!-- عمود القوائم الجانبية -->
            <div class="col-md-4"  style="border-right:1px solid var(--color-primary-light);max-height: 345px;" >
                <ul class="nav flex-column ">
                    <li class="nav-item"><a class="nav-link active" data-target="general">Username</a></li>
                    <li class="nav-item"><a class="nav-link" data-target="edit-profile">Edit Profile</a></li>
                    <li class="nav-item"><a class="nav-link" data-target="password">Password</a></li>
                </ul>
            </div>

            <!-- عمود الأقسام المحتوى -->
            <div class="col-md-8" style="max-height: 345px; padding-left: 45px;">
                <form id="general" class="content-section" action="edit_information.php" method="post">
  <h5>General Settings</h5>

  <input type="hidden" name="form_type" value="general">

  <div class="mb-2">
    <label class="form-label">Username</label>
    <input type="text" name="username" class="form-control" placeholder="UserName" required>
  </div>

  <div class="mb-2">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" placeholder="anything@gmail.com" required>
  </div>

  <div class="mt-4">
    <button type="submit" class="btn btn-primary continue-btn">Save Changes</button>
    <button type="reset" class="btn btn-cancel btn-cance">Cancel</button>
  </div>
</form>


                <!-- 2. قسم Edit Profile -->
<div id="edit-profile" class="content-section">
  <div class="d-flex flex-column align-items-center">
  <img
    src="https://www.w3schools.com/w3images/avatar2.png"
    class="rounded-circle mb-2"
    style="width: 200px; height: 200px;"
    alt="Profile Picture"
    id="profileImageExisting"
  />
</div>
  
<form action="edit_profile.php" method="POST" enctype="multipart/form-data">
      <input type="file" name="photo" accept="image/*" required><br>
      <button type="submit">Upload</button>
        <input type="file" id="fileInput" class="d-none" accept="image/*" />
    </form>

        <?php if (!empty($message)): ?>
      <div class="message <?php echo htmlspecialchars($messageType); ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>
</div>

                <!-- 3. قسم Password -->
                <form id="password" class="content-section" method="post" action="edit_information.php">
  <h5>Password Settings</h5>

  <input type="hidden" name="form_type" value="password">

  <div class="mb-2">
    <label class="form-label">Old Password</label>
    <input type="password" name="old_password" class="form-control" id="oldPassword" placeholder="Old Password" required>
  </div>

  <div class="mb-2">
    <label class="form-label">New Password</label>
    <input type="password" name="new_password" class="form-control" id="newPassword" placeholder="New Password" required>
    <div class="progress">
      <div id="passwordStrengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
    </div>
  </div>

  <div class="mb-2">
    <label class="form-label">Confirm Password</label>
    <input type="password" name="confirm_password" class="form-control" id="confirmPassword" placeholder="Confirm Password" required>
  </div>

  <div class="mt-4">
    <button type="submit" class="btn btn-save" id="savePassword" disabled>Save Changes</button>
    <button type="reset" class="btn btn-cancel btn-cance">Cancel</button>
  </div>
</form>

                
            </div>
        </div>
    </div>

    <!-- لا نُدرج هنا زر "Delete Account" أو أي مودال خاص به؛ تمّت إزالتهما -->

 <div id="footer-placeholder"></div>
  <script>
    fetch('footer.html')
      .then(response => response.text())
      .then(html => {
        const temp = document.createElement('div');
        temp.innerHTML = html;
        const footerEl = temp.querySelector('footer');
        document.getElementById('footer-placeholder').appendChild(footerEl);
      })
      .catch(err => console.error('Error loading footer:', err));
  </script>
    <script>
        $(document).ready(function () {
            // عند التحميل: عرض أول قسم (General) أولاً
            $(".content-section").first().fadeIn();

            // عند النقر على تبويب في الشريط الجانبي: عرض القسم المطابق وإخفاء الباقي
            $(".nav-link").click(function () {
                $(".nav-link").removeClass("active");
                $(this).addClass("active");
                $(".content-section").fadeOut(200, function () {
                    $("#" + $(".nav-link.active").data("target")).fadeIn(200);
                });
            });

            // تهيئة التولتيب لجميع العناصر التي تحمل data-bs-toggle="tooltip"
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // زر تغيير الصورة: يفتح حقل اختيار الملف
            $("#uploadButton").click(function () {
                $("#fileInput").click();
            });
            $("#fileInput").change(function (event) {
                var file = event.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $("#profileImage").attr("src", e.target.result);
                        $("#profileImageExisting").attr("src", e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // زر إزالة الصورة: يعيد الصورة إلى الصورة الافتراضية
            $("#removePhoto").click(function () {
                var defaultSrc = "https://www.w3schools.com/w3images/avatar2.png";
                $("#profileImage").attr("src", defaultSrc);
                $("#profileImageExisting").attr("src", defaultSrc);
            });

            // عداد أحرف حقل البايو
            $("#bio").on("input", function () {
                var len = $(this).val().length;
                $("#charCount").text(len);
                if (len > 960) {
                    $(this).addClass("length-near-limit");
                    $("#charCount").addClass("text-near-limit");
                } else {
                    $(this).removeClass("length-near-limit");
                    $("#charCount").removeClass("text-near-limit");
                }
            });

            // مؤشر قوة كلمة المرور وتمكين/تعطيل زر الحفظ
            $("#newPassword, #confirmPassword").on("input", function () {
                var pwd = $("#newPassword").val();
                var confirm = $("#confirmPassword").val();
                var strengthBar = $("#passwordStrengthBar");
                var strength = 0;

                // منطق بسيط لحساب القوة: بناءً على الطول ووجود حرف كبير ورقم
                if (pwd.length >= 6) strength = 33;
                if (pwd.match(/[A-Z]/)) strength += 33;
                if (pwd.match(/[0-9]/)) strength += 34;

                strengthBar.css("width", strength + "%");
                if (strength < 60) {
                    strengthBar.removeClass("bg-success bg-warning").addClass("bg-danger");
                } else if (strength < 90) {
                    strengthBar.removeClass("bg-success bg-danger").addClass("bg-warning");
                } else {
                    strengthBar.removeClass("bg-warning bg-danger").addClass("bg-success");
                }

                // تمكين زر الحفظ فقط إذا كان الطول ≥ 6 وكلمات المرور متطابقة
                if (pwd.length >= 6 && pwd === confirm) {
                    $("#savePassword").prop("disabled", false);
                } else {
                    $("#savePassword").prop("disabled", true);
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>