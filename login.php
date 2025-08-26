<?php
session_start();
require_once 'includes/config.php';

// Check if already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if(isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]){
        header("location: admin/dashboard.php");
    } else {
        header("location: index.php");
    }
    exit;
}

$email = $password = "";
$login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    if(empty(trim($_POST["email"]))){
        $login_err = "Please enter your email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $login_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    if(empty($login_err)){
        $sql = "SELECT id, username, email, password, is_admin FROM users WHERE email = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            $stmt->bind_param("s", $email);
            
            if($stmt->execute()){
                $stmt->store_result();
                
                if($stmt->num_rows == 1){
                    $stmt->bind_result($id, $username, $db_email, $hashed_password, $is_admin);
                    $stmt->fetch();
                    
                    if(password_verify($password, $hashed_password)){
                        // Password is correct - SET SESSION WITH BOOLEAN VALUES
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        $_SESSION["email"] = $db_email;
                        $_SESSION["is_admin"] = (bool)$is_admin;
                        
                        // Redirect based on admin status
                        if($is_admin){
                            header("location: admin/dashboard.php");
                        } else{
                            header("location: index.php");
                        }
                        exit;
                    } else{
                        $login_err = "Invalid email or password.";
                    }
                } else{
                    $login_err = "Invalid email or password.";
                }
            } else{
                $login_err = "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
}

require_once 'includes/header.php';
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
    <div class="bg-blue-600 py-4 px-6">
        <h2 class="text-white text-2xl font-semibold">Login</h2>
        <p class="text-blue-100">Welcome back!</p>
    </div>
    
    <?php if(!empty($login_err)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative m-4" role="alert">
            <?php echo $login_err; ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_GET['registered']) && $_GET['registered'] == 1): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative m-4" role="alert">
            Registration successful! Please login.
        </div>
    <?php endif; ?>
    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="p-6 space-y-4">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" name="email" id="email" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                   value="<?php echo htmlspecialchars($email); ?>">
        </div>
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" id="password" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
            </div>
            
            <div class="text-sm">
                <a href="#" class="text-blue-600 hover:text-blue-800">Forgot password?</a>
            </div>
        </div>
        
        <div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                Login
            </button>
        </div>
    </form>
    
    <div class="bg-gray-50 px-6 py-4">
        <p class="text-center text-gray-600">
            Don't have an account? 
            <a href="register.php" class="text-blue-600 hover:text-blue-800 font-semibold">Register here</a>
        </p>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>