<?php
require_once 'includes/config.php';

// Initialize variables
$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_err = "";

// Process form data when submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } elseif(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email address.";
    } else{
        // Check if email exists
        $sql = "SELECT id FROM users WHERE email = ?";
        if($stmt = $mysqli->prepare($sql)){
            $stmt->bind_param("s", $param_email);
            $param_email = trim($_POST["email"]);
            if($stmt->execute()){
                $stmt->store_result();
                if($stmt->num_rows == 1){
                    $email_err = "This email is already registered.";
                } else{
                    $email = trim($_POST["email"]);
                }
            }
            $stmt->close();
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Generate username from email (first part before @)
        $username = strtok($email, '@');
        
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        if($stmt = $mysqli->prepare($sql)){
            $stmt->bind_param("sss", $username, $email, $param_password);
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            
            if($stmt->execute()){
                // Redirect to login page
                header("location: login.php?registered=1");
                exit;
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
}

require_once 'includes/header.php';
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
    <div class="bg-blue-600 py-4 px-6">
        <h2 class="text-white text-2xl font-semibold">Create Account</h2>
        <p class="text-blue-100">Join us today!</p>
    </div>
    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="p-6 space-y-4">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" name="email" id="email" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>"
                   value="<?php echo htmlspecialchars($email); ?>">
            <span class="text-red-500 text-sm"><?php echo $email_err; ?></span>
        </div>
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" id="password" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>">
            <span class="text-red-500 text-sm"><?php echo $password_err; ?></span>
        </div>
        
        <div>
            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?php echo (!empty($confirm_password_err)) ? 'border-red-500' : ''; ?>">
            <span class="text-red-500 text-sm"><?php echo $confirm_password_err; ?></span>
        </div>
        
        <div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                Register
            </button>
        </div>
    </form>
    
    <div class="bg-gray-50 px-6 py-4">
        <p class="text-center text-gray-600">
            Already have an account? 
            <a href="login.php" class="text-blue-600 hover:text-blue-800 font-semibold">Login here</a>
        </p>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>