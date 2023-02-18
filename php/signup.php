<?php
    session_start();
    include_once "config.php";
    $fname=mysqli_real_escape_string($conn, $_POST['fname']);
    $lname=mysqli_real_escape_string($conn, $_POST['lname']);
    $email=mysqli_real_escape_string($conn, $_POST['email']);
    $password=mysqli_real_escape_string($conn, $_POST['password']);
    
    if(!empty($fname) && !empty($lname) &&!empty($email) && !empty($password)){
        // let chech user email is valid or not
        if(filter_var($email,FILTER_VALIDATE_EMAIL)){//if email is valid
            // lets chec that email already exist in the database or not
            $sql=mysqli_query($conn,"SELECT * from users WHERE email='{$email}'");
            if(mysqli_num_rows($sql)>0){
                echo "$email - This email already exist";
            }else{
                // lets check user upload file or not
                if(isset($_FILES['image'])){//if file is uploaded
                    $img_name=$_FILES['image']['name'];//getting user uploaded img name
                    $img_type=$_FILES['image']['type'];//getting user uploaded img type
                    $tmp_name=$_FILES['image']['tmp_name'];//this temporary name is used to save/move file in our folder

                    //lets explode image and get the last  name like jpg png
                    $img_eplode=explode('.',$img_name);
                    $img_ext=end($img_eplode);//here we get the extension of an user uploaded img file

                    $extensions=["jpeg", "png",  "jpg"]; //these are some valid img ext and we've store the in array
                    if(in_array($img_ext, $extensions) === true) { //if user uploaded img ext is matched with any array extensions
                        $types=["image/jpeg","image/jpg","image/png"];
                        if(in_array($img_type,$types) === true){
                            $time=time();//this will return us current time..
                        //we need this time because when you uploading user img to our folder we rename user file with current time 
                        //so all the img file will have unique name
                        //lets move the user uploaded img to our particular folder
                        $new_img_name=$time.$img_name;
                        if(move_uploaded_file($tmp_name,"images/".$new_img_name)) {//if user upload img movo to our folder successfully
                            $random_id=rand(time(),100000000);//creating random id for user
                            $status="Active now";//once user signed up then his status will be active now
                            //lets insert all user data inside table
                            $sql2=mysqli_query($conn,"INSERT INTO users (uniqe_id,fname,lname,email,password,img,status)
                                              VALUES ({$random_id}, '{$fname}', '{$lname}', '{$email}', '{$password}', '{$new_img_name}', '{$status}')");
                            if($sql2) { //if these data inserted
                                $sql3=mysqli_query($conn,"SELECT * FROM users WHERE email ='{$email}'");
                                if(mysqli_num_rows($sql3) > 0 ) {
                                    $row = mysqli_fetch_assoc($sql3);
                                    $_SESSION['unique_id']=$row['unique_id'];//using this session we used user unique_id in other php file
                                    echo "success";
                                }
                            }else{
                                echo "Something went wrong!";
                            }
                        }
                     }
                    }else{
                        echo "Please select an Image file - jpeg, jpg, png!";
                    }
                }else{
                    echo "Please select an Image File!";
                }
            }
        }else{
            echo "$email - This is not a valid email!";
        }
    }else{
        echo "All input field are required!";
    }
?>