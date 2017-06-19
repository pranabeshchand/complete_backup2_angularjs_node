<?php
header('Access-Control-Allow-Origin: *');   
ini_set('memory_limit', '-1');
App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');
App::uses('CakeEmail', 'Network/Email');
class IosController extends AppController { 
     public $components = array('Auth', 'Paginator', 'Session', 'Date', 'Datamail','Timezone');
        var $helpers = array('Html','Time');
    public function beforeFilter(){
        parent::beforeFilter();
        $this->Auth->allow(array('login','forgetpassword','reset','timer_countup','signup','homepostphoto','mypost','supp_unsupp','suppoter_unsuppoter','commentlist','emooji','post_edit','post_delete','about','edit_profile','help','photo','livemeetinglist','livemeeting','supports','onlinemember','sharepost','livechat_add','autosearchtop','profileimg_save_to_file','terms','coutactus','needsponsers','changepassword','publicpost','isupport','viewmyfriend','becomeAsponser','faq','groupsupport_unsupport','share_post_onhomepage','blockmysupport','delete_supporter','chat_load','chatunread_mesagecount','chat1_typing','chat1_list','chat1_delete','msg_post','chat1_photo','changecoverphoto','iosFCMMessage','androidFCMMessage','sendFCMMessage'));
    }    
    public function signup(){
       Configure::write('debug', 0);
        $this->layout="ajax";
        $this->loadModel('User'); 
        if ($this->request->is('post')) {
            if ($this->User->hasAny(array('User.email' => $this->request->data['User']['email']))) {
                $response['msg']="Email Already exist";
                $response['error']="2";
            } else {
                $this->User->create();
                $this->request->data['User']['status'] = '1';
                $this->request->data['User']['email_varification'] = 0;
                $this->request->data['User']['role'] = 'User';
                $this->request->data['User']['color'] = '#e7e7e7';
                $this->request->data['User']['last_login'] = '0';
                $this->request->data['User']['addtimezone'] = $this->request->data['User']['yr'] . '-' . $this->request->data['User']['mont'] . '-' . $this->request->data['User']['day'];
                $this->request->data['User']['time_addict_alco']=date("h:m:s");
                $this->request->data['User']['signup_complete']='0';
                if ($this->User->save($this->request->data)) {
//                    $this->Session->setFlash(__('Registered Successfully.. Check your inbox for your login details...'));
                    $ids = $this->User->getLastInsertId();
//                    debug($ids); exit;
                    
                    $key = Security::hash(String::uuid(), 'sha512', true);
                        $hash = sha1($this->request->data['User']['firstname'] . rand(0, 100));
                        $url = FULL_BASE_URL . $this->webroot . 'Users/confirm/'.$ids.'/'. $key . '#' . $hash;
                        $ms = "Thank you for choosing to join mysponsers.com 
                                    Please, <strong><a href=" . $url . ">Click this link to verify your email address</a></strong>, or your account will be deleted.";
//                        $fu['User']['tokenhash'] = $key;
                        $this->User->id = $ids;
                        if ($this->User->saveField('tokenhash', $key)) {
                            try{
                              $l = new CakeEmail('smtp');
//                            debug($l); exit;
                            $l->emailFormat('html')->template('default', 'default')->subject('Email Verification')->to($this->request->data['User']['email'])->send($ms);
                            $this->set('smtp_errors', "none");  
                        }catch(Exception $e){ 
                            
                        }
                        $response['id']=$ids;
                        $response['msg']="Registered Successfully.. Check your inbox for your login details...";
                        $response['error']="1";
                        }
//                        return $this->redirect(array('controller'=>'users','action'=>'autologin'.'/'.$ids));
//                    return $this->redirect(array('controller' => 'users', 'action' => 'step1' . '/' . $ids));    
                    //return $this->redirect(array('controller' => 'users', 'action' => 'email_varification'));
                } else {
                    $response['msg']="The user could not be saved. Please, try again.";
                    $response['error']="0"; 
                }
            }
        }
        $this->set("response",$response);
        $this->render('ajax');
    }
    public function login(){
        Configure::write('debug', 0);
         $this->layout = "ajax";
        if ($this->request->is('post')){
            $this->loadModel('User');
            $role=$this->User->find('first',array('conditions'=>array('User.email'=>$this->request->data['User']['email']),'recursive'=>0));
            if(!empty($role) && ($role['User']['status']=='1')){
             if ($this->Auth->login()) {
                $emailFromUsername = $role;
                $this->User->id = $emailFromUsername['User']['id'];
                // debug($this->request->data['User']['device_token']) ;
                $this->User->saveField('device_token', $this->request->data['User']['device_token']);
                $emailFromUsername['User']['device_token'] = $this->request->data['User']['device_token'];
                $fileExtention = pathinfo ( $emailFromUsername['User']['image'], PATHINFO_EXTENSION );
                    if($fileExtention):
                    $emailFromUsername['User']['image']=FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$emailFromUsername['User']['image'];
                    else:
                    $emailFromUsername['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                    endif;
                    $fileExtention = pathinfo ( $emailFromUsername['User']['coverphoto'], PATHINFO_EXTENSION );
                    if($fileExtention):
                    $emailFromUsername['User']['coverphoto']=FULL_BASE_URL . $this->webroot ."files".DS."coverphoto".DS.$emailFromUsername['User']['coverphoto'];
                    else:
                    $emailFromUsername['User']['coverphoto']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."cover_photo1.jpg";
                    endif; 
                    if($emailFromUsername['User']['addict_alco_type']=="Addict"){
                        $emailFromUsername['User']['addict_alco_type']="Time Clean";
                        $time = strtotime($emailFromUsername['User']['addtimezone'].' '.$emailFromUsername['User']['time_addict_alco']);
                        $month1 = date("Y-m-d H:i:s", strtotime("+30 day", $time));
                        $month2 = date("Y-m-d H:i:s", strtotime("+60 day", $time));
                        $month3 = date("Y-m-d H:i:s", strtotime("+90 day", $time));
                        $month6  = date("Y-m-d H:i:s", strtotime("+180 day", $time));
                        $month9  = date("Y-m-d H:i:s", strtotime("+270 day", $time));
                        $month12 = date("Y-m-d H:i:s", strtotime("+365 day", $time));
                        //        $year1   = date("Y-m-d H:i:s", strtotime("+365 day", $time));
                        $month18 = date("Y-m-d H:i:s", strtotime("+540 day", $time));
                        $year2 = date("Y-m-d H:i:s", strtotime("+720  day", $time));
                        $year3 =date("Y-m-d H:i:s", strtotime("+1080  day", $time)); 
                        $year4 = date("Y-m-d H:i:s", strtotime("+1440  day", $time));
                        $current_date = date('Y-m-d H:i:s');
                          
                        if(strtotime($current_date) <= strtotime($month1)){
                         $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/1.png";     
                        }elseif(strtotime($current_date) <= strtotime($month2)) {
                        $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/2.png"; 
                        } elseif(strtotime($current_date) <= strtotime($month3)) {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/3.png";  
                        } elseif($current_date <= $month6) {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/4.png"; 
                        } elseif($current_date <= $month9) {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/5.png";  
                        } elseif($current_date <= $month12) {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/6.png"; 
                        } elseif($current_date <= $month18) {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/7.png";   
                        } elseif($current_date <= $year2) {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/8.png";  
                        } elseif($current_date <= $year3) {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/9.png";
                        } else {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/9.png"; 
                        }
                         $emailFromUsername['User']['addtimezone']=date("Y-m-d H:i:s",$time);   
                    }else{
                      $emailFromUsername['User']['addict_alco_type']="Time Sober";  
                        $time_alcho = strtotime($emailFromUsername['User']['addtimezone'].' '.$emailFromUsername['User']['time_addict_alco']);

                        $hoursblank_alcho = date("Y-m-d H:i:s", strtotime("+24 hours", $time_alcho));
                        $hours24_alcho = date("Y-m-d H:i:s", strtotime("+30 day", $time_alcho));
                        $month1_alcho = date("Y-m-d H:i:s", strtotime("+60 day", $time_alcho));
                        $month2_alcho = date("Y-m-d H:i:s", strtotime("+90 day", $time_alcho));
                        $month3_alcho  = date("Y-m-d H:i:s", strtotime("+180 day", $time_alcho));
                        $month6_alcho  = date("Y-m-d H:i:s", strtotime("+270 day", $time_alcho));
                        $month9_alcho = date("Y-m-d H:i:s", strtotime("+365 day", $time_alcho));
                        $year1_alcho   = date("Y-m-d H:i:s", strtotime("+366 day", $time_alcho));
                        $year2_alcho = date("Y-m-d H:i:s", strtotime("+720 day", $time_alcho));
                        $year3_alcho = date("Y-m-d H:i:s", strtotime("+1080 day", $time_alcho));

                        $current_date_alcho = date('Y-m-d H:i:s');
                        if($current_date_alcho <= $hoursblank_alcho){
//                                    $emailFromUsername['User']['timerflip']= FULL_BASE_URL .$this->webroot."images/coin/1.png";
                        } elseif($current_date_alcho <= $hours24_alcho){
                        $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/1.png";            
                        } elseif($current_date_alcho <= $month1_alcho) {
                        $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/2.png";   
                        } elseif($current_date_alcho <= $month2_alcho) {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/3.png";  
                        } elseif($current_date_alcho <= $month3_alcho) {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/4.png";  
                        } elseif($current_date_alcho <= $month6_alcho) {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/5.png";  
                        } elseif($current_date_alcho <= $month9_alcho) {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/6.png";  
                        } elseif($current_date_alcho <= $year1_alcho) {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/7.png";  
                        } elseif($current_date_alcho <= $year2_alcho) {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/7.png";  
                        } else {
                          $emailFromUsername['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/7.png";
                        }
                      $emailFromUsername['User']['addtimezone']=date("Y-m-d H:i:s",$time_alcho);
                    } 
                    $current_date = date('Y-m-d H:i:s');
                    $datetime1 = date_create($emailFromUsername['User']['addtimezone']);
                    $datetime2 = date_create($current_date);
                    $interval = date_diff($datetime1, $datetime2); 
                   $emailFromUsername['User']['days_difference']= $interval->format('%a days %h hours %i minutes %s seconds');
                   $sxa=$interval->format('%a');
                   if($sxa==0){$emailFromUsername['User']['timerflip']=""; }
//                              %R  if(empty($emailFromUsername['User']['addtimezone'].' '.$loggeduser['User']['time_addict_alco'])){
//                                $emailFromUsername['User']['addtimezone']=date('y-m-d h:i:s',$emailFromUsername['User']['created']);
//                                }
                    $response['login']=$emailFromUsername;
                    $response["msg"]="Logged in Successfully";
                    $response['error']=1; 
                } else {
                    $response["msg"]="Login Failed: Invalid Login Info.";
                    $response['error']=0;
                   // $this->Session->setFlash('Your email/password combination was incorrect');
                }
            }else{
               $response["msg"]="You have been blocked from this site for unwanted behavior, someone from our team will be contacting you soon.";
               $response['error']=0;
            }
        }else{ 
            $response["msg"]="Login Failed: Invalid Login Info.";
            $response['error']=0;
         }
        $this->set('response',$response);
        $this->render('ajax');
    }
    public function forgetpassword() {
        Configure::write('debug', 0);
        $this->layout="ajax";
        $this->loadModel('User');
        $this->User->recursive = -1;
        if (!empty($this->data)) {
            if (empty($this->data['User']['email'])) {
                $response['error']=0;
                $response['msg']="Please Provide Your Email Address that You used to Register with Us";
             } else {
                $email = $this->data['User']['email'];
                $fu = $this->User->find('first', array('conditions' => array('User.email' => $email),'fields' => array('id','email','status','firstname','lastname','image','tokenhash','role'),'recursive'=>-1));
                if ($fu) {
                    if ($fu['User']['status'] == "1") {
                        $key = Security::hash(String::uuid(), 'sha512', true);
                        $hash = sha1($fu['User']['firstname'] . rand(0, 100));
                        $url = Router::url(array('controller' => 'users', 'action' => 'reset'), true) . '/' . $key . '#' . $hash;
                        $ms = "<p>You are receiving this email as you have requested a change of password
                                                    <br/> If you have not requested this change please ignore this email.
                                                   Click the link below to reset your password...</p><p style='width:100%;'> 
                                                    <a href=" . $url . " style='text-decoration:none'><b>Click me to reset your password.</b></a></p>";
                        $fu['User']['tokenhash'] = $key;
                        $this->User->id = $fu['User']['id'];
                        if ($this->User->saveField('tokenhash', $fu['User']['tokenhash'])) {
                            try{
                            $l = new CakeEmail('smtp');
                            $l->emailFormat('html')->template('default', 'default')->subject('Reset Your Password')->to($fu['User']['email'])->send($ms);
                            $this->set('smtp_errors', "none");
                        }catch(Exception $e){ }
                            $response['error']=1;
                            $response['msg']="You can also Reset the password through Email Too"; 
                            $response['list']=$fu;
                            //$this->redirect(array('controller' => 'Pages', 'action' => 'display'));
                        } else {
                            $response['error']=0;
                            $response['msg']="Error Generating Reset link";
                            
                         }
                    } else {
                        $response['error']=0;
                        $response['msg']="This Account is Blocked. Please Contact to Administrator..."; 
                    }
                } else {
                    $response['error']=0;
                    $response['msg']="Email does Not Exist";
                 }
            }
        }
        $this->set("response",$response);
        $this->render('ajax');
    }
    public function reset($token = null) {
        Configure::write('debug', 0);
        $this->layout="ajax";
        $this->loadModel('User');
        $this->User->recursive = -1;
        if (!empty($token)) {
            $u = $this->User->findBytokenhash($token);
            if ($u) {
                $this->User->id = $u['User']['id'];
                if (!empty($this->data)) {
                    if ($this->data['User']['password'] != $this->data['User']['password_confirm']) {
                        $response['error']=0;
                        $response['msg']="Password doesnot match.";
                        $this->set("response",$response);
                        $this->render('ajax');
                        exit;
                    }
                    $this->User->data = $this->data;
                    $this->User->data['User']['email'] = $u['User']['email'];
                    $new_hash = sha1($u['User']['email'] . rand(0, 100)); //created token
                    $this->User->data['User']['tokenhash'] = $new_hash;
                    if ($this->User->validates(array('fieldList' => array('password', 'password_confirm')))) {
                        if ($this->User->save($this->User->data)) {
//                            $this->Session->setFlash('Password Has been Updated. For login <a href="/" style="color: rgb(255, 255, 255); text-decoration: underline;">Click Here</a>');
//                            $this->redirect(array('controller' => 'Users', 'action' => 'login'));
                            $response['error']=1;
                            $response['msg']='Password Has been Updated.';
                        }
                    } else {
                        $response['error']=0;
                        $response['msg']='Invalid field';
//                        $this->set('errors', $this->User->invalidFields());
                    }
                }
            } else {
                $response['error']=0;
                $response['msg']='Token Corrupted.The reset link work only for once.';
             }
        } else {
            $response['error']=0;
            $response['msg']='Pls try again...';
         }
        $this->set("response",$response);
        $this->render('ajax');
    }
    public function logout() {
     Configure::write("debug",0);
    $this->lauout="ajax";    
    if($this->Auth->logout()){ 
        $response["msg"]="Logout Success";
        $response['error']=1;
    }else{
        $response["msg"]="Logout Failed";
        $response['error']=0;
    }
   $this->set("response",$response);
   $this->render("ajax");
  }
  public function timer_countup($id=null){
     Configure::write("debug",0);
    $this->layout="ajax"; 
    $this->loadModel("User");
    if(!empty($id)):
        $tim=$this->User->find('first',array('conditions'=>array('User.id'=>$id),
            'fields'=>array('User.id','User.addtimezone','User.time_addict_alco','User.firstname','User.lastname','User.email','User.image','User.coverphoto','User.addict_alco_type'),'recursive'=>-1));
        if(!empty($tim['User']['image'])){
        $tim['User']['image']=FULL_BASE_URL . $this->webroot ."files".DS.'profile'.DS.$tim['User']['image'];
        }else{
        $tim['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";    
        }
        if(!empty($tim['User']['coverphoto'])){
        $tim['User']['coverphoto']=FULL_BASE_URL . $this->webroot ."files".DS."coverphoto".DS.$tim['User']['coverphoto'];
        }else{
        $tim['User']['coverphoto']=FULL_BASE_URL . $this->webroot ."inner".DS.'images'.DS.'cover_photo1.jpg';    
        }
        
        if($tim['User']['addict_alco_type']=="Addict"){
        $tim['User']['addict_alco_type']="Time Clean";
            $time = strtotime($tim['User']['addtimezone'].' '.$tim['User']['time_addict_alco']);
            $month1 = date("Y-m-d H:i:s", strtotime("+30 day", $time));
            $month2 = date("Y-m-d H:i:s", strtotime("+60 day", $time));
            $month3 = date("Y-m-d H:i:s", strtotime("+90 day", $time));
            $month6  = date("Y-m-d H:i:s", strtotime("+180 day", $time));
            $month9  = date("Y-m-d H:i:s", strtotime("+270 day", $time));
            $month12 = date("Y-m-d H:i:s", strtotime("+365 day", $time));
            //        $year1   = date("Y-m-d H:i:s", strtotime("+365 day", $time));
            $month18 = date("Y-m-d H:i:s", strtotime("+540 day", $time));
            $year2 = date("Y-m-d H:i:s", strtotime("+720  day", $time));
            $year3 =date("Y-m-d H:i:s", strtotime("+1080  day", $time)); 
            $year4 = date("Y-m-d H:i:s", strtotime("+1440  day", $time));
            $current_date = date('Y-m-d H:i:s');
            if(strtotime($current_date) <= strtotime($month1)){
             $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/1.png";    
            }elseif(strtotime($current_date) <= strtotime($month2)) {
            $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/2.png";   
            } elseif(strtotime($current_date) <= strtotime($month3)) {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/3.png"; 
            } elseif($current_date <= $month6) {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/4.png";
            } elseif($current_date <= $month9) {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/5.png"; 
            } elseif($current_date <= $month12) {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/6.png"; 
            } elseif($current_date <= $month18) {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/7.png";  
            } elseif($current_date <= $year2) {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/8.png";  
            } elseif($current_date <= $year3) {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/9.png";
            } else {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/icon/9.png"; 
            }
             $tim['User']['addtimezone']=date("Y-m-d H:i:s",$time);  
        }else{
          $tim['User']['addict_alco_type']="Time Sober";  
            $time_alcho = strtotime($tim['User']['addtimezone'].' '.$tim['User']['time_addict_alco']); 
            $hoursblank_alcho = date("Y-m-d H:i:s", strtotime("+24 hours", $time_alcho));
            $hours24_alcho = date("Y-m-d H:i:s", strtotime("+30 day", $time_alcho));
            $month1_alcho = date("Y-m-d H:i:s", strtotime("+60 day", $time_alcho));
            $month2_alcho = date("Y-m-d H:i:s", strtotime("+90 day", $time_alcho));
            $month3_alcho  = date("Y-m-d H:i:s", strtotime("+180 day", $time_alcho));
            $month6_alcho  = date("Y-m-d H:i:s", strtotime("+270 day", $time_alcho));
            $month9_alcho = date("Y-m-d H:i:s", strtotime("+365 day", $time_alcho));
            $year1_alcho   = date("Y-m-d H:i:s", strtotime("+366 day", $time_alcho));
            $year2_alcho = date("Y-m-d H:i:s", strtotime("+720 day", $time_alcho));
            $year3_alcho = date("Y-m-d H:i:s", strtotime("+1080 day", $time_alcho));

            $current_date_alcho = date('Y-m-d H:i:s');
            if($current_date_alcho <= $hoursblank_alcho){
            $tim['User']['timerflip']= FULL_BASE_URL .$this->webroot."images/coin/1.png";
            } elseif($current_date_alcho <= $hours24_alcho){
            $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/1.png";            
            } elseif($current_date_alcho <= $month1_alcho) {
            $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/2.png";   
            } elseif($current_date_alcho <= $month2_alcho) {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/3.png";  
            } elseif($current_date_alcho <= $month3_alcho) {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/4.png";  
            } elseif($current_date_alcho <= $month6_alcho) {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/5.png";  
            } elseif($current_date_alcho <= $month9_alcho) {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/6.png";  
            } elseif($current_date_alcho <= $year1_alcho) {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/7.png";  
            } elseif($current_date_alcho <= $year2_alcho) {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/7.png";  
            } else {
              $tim['User']['timerflip']=FULL_BASE_URL .$this->webroot."images/coin/7.png";
            }
          $tim['User']['addtimezone']=date("Y-m-d H:i:s",$time_alcho);
        }
        $current_date = date('Y-m-d H:i:s');
        $datetime1 = date_create($tim['User']['addtimezone']);
        $datetime2 = date_create($current_date);
        $interval = date_diff($datetime1, $datetime2); 
       $tim['User']['days_difference']= $interval->format('%a days %h hours %i minutes %s seconds');
        $response['list']=$tim;
        $response['error']=1;
        $response['msg']="Success";
    else: 
        $response['error']=0;
        $response['msg']="failed";
    endif;
    $this->set('response',$response);
    $this->render('ajax');
  }
  public function homepostphoto($user_id=NULL) { 
     //echo ini_get('upload_max_filesize'), ", " , ini_get('post_max_size');die;
        $this->layout = "ajax"; 
        Configure::write('debug', 0);
        $this->loadModel('Post');
        if ($this->request->is('post')) {
            //  debug($this->request->data);
            $this->Post->create();
            $width = array();
            $height = array(); //$z = 0;
            $heights = @$this->request->data['Post']['height'];
            if($heights){
                foreach ($heights as $heights1) {
                     $height[] = $heights1;
                }
            }
            $widths = @$this->request->data['Post']['width'];
            if($widths){
                foreach ($widths as $width1) {
                    $width[] = $width1;  
                }
            }

            $images = @$this->request->data['Post']['photo']; 
             if($images){
            foreach ($images as $image) {
                $image_name = date("YmdThis");
                $target_dir = "files/postphoto/";
                $target_file = $target_dir . basename($image["name"]);

                $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                $img = str_replace(".$imageFileType", "-", $image["name"]) . $image_name;
                $img = str_replace(' ', '-', $img);
                $img = preg_replace('/[^A-Za-z0-9\-]/', '', $img) . "." . $imageFileType;
               /* $checkq = @getimagesize($image["tmp_name"]); 
                
                if(isset($checkq)){
                 $width[] =  @$checkq[0];
                 $height[] = @$checkq[1];    
                }else{
                 $width[] =  $this->request->data['Post']['width'][$z]; 
                 $height[] = $this->request->data['Post']['height'][$z]; 
                } $z++; */
                if(move_uploaded_file($image['tmp_name'], WWW_ROOT . 'files/postphoto/' . $img))
                 { $srrr = "Media file successfully uploaded"; }else{ $srrr = "Media file failed upload"; }
              $damVideo = explode('.',$img);
 
          if( $damVideo[1] == 'mp4' ) {
              $video = WWW_ROOT . 'files/postphoto/' . escapeshellcmd($img);
            $cmd = "/usr/local/bin/ffmpeg -i /home/mysponsers/public_html/app/webroot/files/postphoto/".$img." 2>&1";
            $second = 1;
            $time = strtotime(date('y-m-d h:i:s'));
            if (preg_match('/Duration: ((\d+):(\d+):(\d+))/s', `$cmd`, $time)) {
                $total = ($time[2] * 3600) + ($time[3] * 60) + $time[4];
                $second = rand(1, ($total - 1));
            }
            $image  = WWW_ROOT . 'files/postphoto/videothumbnail/' .$damVideo[0].'.jpg';
            $cmd = "/usr/local/bin/ffmpeg -i $video -deinterlace -an -ss $second -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg $image 2>&1";
           shell_exec($cmd);

              $img = $damVideo[0].".mp4";
              $images_arr[] = $img;
              $image_type_arr[] = $img;/*$image['type'];*/
          }    
         
          else if( $damVideo[1] === 'flv' || $damVideo[1] === 'vob' || $damVideo[1] === 'gif' || $damVideo[1] === 'avi' || $damVideo[1] === 'wmv' || $damVideo[1] === 'mpeg' || $damVideo[1] === '3gp' || $damVideo[1]=== '3gpp' || $damVideo[1] === 'mkv')
          {
          
           shell_exec("/usr/local/bin/ffmpeg -i  /home/mysponsers/public_html/app/webroot/files/postphoto/".$img." -s 600x400 -vcodec h264 -acodec aac -strict -2 /home/mysponsers/public_html/app/webroot/files/postphoto/".$damVideo[0].".mp4");


            $video = WWW_ROOT . 'files/postphoto/' . escapeshellcmd($damVideo[0].".mp4");
            $cmd = "/usr/local/bin/ffmpeg -i /home/mysponsers/public_html/app/webroot/files/postphoto/".$damVideo[0].".mp4"." 2>&1";
            $second = 1;
            $time = strtotime(date('y-m-d h:i:s'));
            if (preg_match('/Duration: ((\d+):(\d+):(\d+))/s', `$cmd`, $time)) {
                $total = ($time[2] * 3600) + ($time[3] * 60) + $time[4];
                $second = rand(1, ($total - 1));
            }
            $image  = WWW_ROOT . 'files/postphoto/videothumbnail/' .$damVideo[0].'.jpg';
            $cmd = "/usr/local/bin/ffmpeg -i $video -deinterlace -an -ss $second -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg $image 2>&1";
           shell_exec($cmd);
 
           /*--Generating poster image-----*/

           /*---End poster image-----------*/

              unlink(WWW_ROOT . 'files/postphoto/' . $img);  
              $img = $damVideo[0].".mp4";
              $images_arr[] = $img;
              $image_type_arr[] = $img;/*$image['type'];*/
              }else{

                $images_arr[] = $img;
                $image_type_arr[] = $image['type'];
                // create the thumbnail
                
            }
              
            }
             }else{ $srrr = "Text successfully uploaded"; }
            $this->request->data['Post']['photo'] = @serialize($images_arr);
            foreach ($image_type_arr as $image_type) {
                if (($image_type == "image/png") || ($image_type == "image/jpg") || ($image_type == "image/jpeg") || ($image_type == "image/gif")) {
                    $this->request->data['Post']['type'] = "image";
               

                } else if ( ( $damVideo[1] == "mp4") || ($damVideo[1] == "flv") || ($damVideo[1] == "wav") || ($damVideo[1] == "vob") || ($damVideo[1] == "gif") || ($damVideo[1] == "avi") || ($damVideo[1] == "wmv") || ($damVideo[1] == "mpeg") || ($damVideo[1] == "mkv") || ($damVideo[1] == "3gp")) {
                    //$this->request->data['Post']['type'] = "video/".$damVideo[1];
                 $this->request->data['Post']['type'] = "video";


                } else {
                    $this->request->data['Post']['type'] = $image_type;
                }
            }
            $this->request->data['Post']['width'] = @serialize($width);
            $this->request->data['Post']['height'] = @serialize($height);  
            $this->request->data['Post']['status'] = '1';
            $this->request->data['Post']['user_id'] = $user_id;
            $this->request->data['Post']['thumbnail'] = ""; 
            if ($this->Post->save($this->request->data)) {

                $fistd = $this->Post->find('first',array('conditions'=>array('Post.user_id'=>$user_id),'fields'=>array('Post.id','Post.user_id','Post.post','Post.photo','Post.ref_id','Post.type','Post.width','Post.height','Post.thumbnail'),'order'=>array('Post.id'=>'DESC'),'recursive'=>-1));
                    if(!empty($fistd)){  
                      foreach ($fistd as $fistds) {
                        if($fistds['type']=="image"){
                                if(!empty($fistds['photo'])){
                                   $po=unserialize($fistds['photo']); 
                                   $width=unserialize($fistds['width']);
                                   $height=unserialize($fistds['height']);
                                   // print_r($po); exit;
                                   $a=array(); $k = 0;
                                   foreach ($po as $pos) { 
                                       $fileExtention = pathinfo ( $pos, PATHINFO_EXTENSION ); 
                                       if(!empty($fileExtention)){
                                        $a[]=FULL_BASE_URL . $this->webroot . 'files'.DS.'postphoto'.DS.$pos; 
                                        $widt[] = $width[$k];
                                        $higt[] = $height[$k];
                                       }else{
                                        $a[]= "";
                                        $widt[] = "";
                                        $higt[] = "";
                                       }
                                       $k++;
                                   }
                                   $fistds['photo']=$a; 
                                   $fistds['width']=$widt; 
                                   $fistds['height']=$higt; 
                                }else{
                                 $fistds['photo'] = array(); 
                                 $fistds['width']=$widt; 
                                 $fistds['height']=$higt; 
                                }
                                
                            }
                        elseif($fistds['type']=="video"){
                                if(!empty($fistds['photo'])){
                                   $po=unserialize($fistds['photo']); 
                                   $width=unserialize($fistds['width']);
                                   $height=unserialize($fistds['height']);
                                   // print_r($po);debug($height); exit; 
                                   $a=array(); $k = 0;
                                   foreach ($po as $pos) { 
                                       $fileExtention = pathinfo ( $pos, PATHINFO_EXTENSION ); 
                                       if(!empty($fileExtention)){
                                        $a[]=FULL_BASE_URL . $this->webroot . 'files'.DS.'postphoto'.DS.$pos;
                                        $widt[] = $width[$k];
                                        $higt[] = $height[$k]; 
                                       }else{
                                        $a[]= "";
                                        $widt[] = "";
                                        $higt[] = "";
                                       }
                                       $k++;
                                   }
                                   $fistds['photo']=$a; 
                                   $fistds['thumbnail'] = array();
                                   $fistds['width']=$widt; 
                                   $fistds['height']=$higt; 
                                }else{
                                 $fistds['photo'] = array(); 
                                 $fistds['thumbnail'] = array(); 
                                 $fistds['width']=$widt; 
                                 $fistds['height']=$higt; 
                                }
                                
                            }else{
                             $fistds['photo'] = array(); 
                             $fistds['thumbnail'] = array(); 
                            }    
                      }
                      $response['lastpost'] = $fistds;
                    }else{
                      $response['lastpost'] = "";
                    }

                $response['msg'] = $srrr;
                $response['error'] =1;
                //$response['ifwantToCancel'] = $cancel_id;
            } else {
                $response['msg'] = "failed";
                $response['error'] = 0;
            }
            
        }else{
            $response['error']=0;
            $response['success'] = "Invalid post";
        }
        $this->set('response', $response);
            $this->render('ajax');
   
    }public function mypost($userid=NULL,$pagi=null){
        configure::write('debug',0);
        $this->layout="ajax";
        $this->loadModel('User');
        $this->loadModel('Post');
        $this->loadModel('Support');
        $this->loadModel('Share');
        $this->loadModel('ShareWith');
        $this->loadModel('Comment');
        $this->loadModel('Like');
        $pub=$this->Post->find('all',array('conditions'=>array('Post.user_id'=>$userid),'contain'=>array('User'=>array('fields'=>array('User.id','User.firstname','User.lastname','User.email','User.image')),'Like','ShareWith'=>array('fields'=>array('ShareWith.id','ShareWith.firstname','ShareWith.lastname','ShareWith.email','ShareWith.image')),'Comment'),'order'=>array('Post.id'=>'DESC'),'limit'=>10,'offset' =>$pagi));
        if(!empty($pub)){
            $i=0;
            $com=""; //debug($pub); exit;
            foreach ($pub as $pubs) { 
                /*if(!empty($pubs['Post']['post'])){
                preg_match('/src="([^"]+)"/', $pubs['Post']['post'], $match);
                if(!empty($match)){
                // debug($match[1]); 
                       $pubs['Post']['post'] = "";
                       $pub[$i]['Post']['post'] = $match[1];
                    }
                            }*/
                if($pubs['Post']['type']=="image"){
                    if(!empty($pubs['Post']['photo'])){
                       $po=unserialize($pubs['Post']['photo']);
                       $width = unserialize($pubs['Post']['width']);
                       $height = unserialize($pubs['Post']['height']); 

                       $a=array(); $k = 0; $widt = array(); $higt = array();
                       foreach ($po as $pos) { 
                           $fileExtention = pathinfo ( $pos, PATHINFO_EXTENSION );
                           if(!empty($fileExtention)){
                            $a[]=FULL_BASE_URL . $this->webroot . 'files'.DS.'postphoto'.DS.$pos;
                            if($width[$k])
                            $widt[] = $width[$k];
                            else $widt[] = 0;
                            if($height[$k])
                            $higt[] = $height[$k]; 
                            else $higt[] = 0; 
                           }else{
                            $a[]= "";
                            $widt[] = "";
                            $higt[] = "";
                           }
                           $k++; 
                       }
                       $pub[$i]['Post']['photo']=$a;
                       $pub[$i]['Post']['thumbnail'] = array();
                       $pub[$i]['Post']['width']=$widt; 
                       $pub[$i]['Post']['height']=$higt;
                    }
                    
                }
                if($pubs['Post']['type']=="video"){
                    if(!empty($pubs['Post']['photo'])){
                       $po=unserialize($pubs['Post']['photo']);
                       $width=unserialize($pubs['Post']['width']);
                       $height=unserialize($pubs['Post']['height']); 
                       // print_r($width); print_r($k); exit;
                       $a=array(); $k = 0;
                       foreach ($po as $pos) {
                           $fileExtention = pathinfo ( $pos, PATHINFO_EXTENSION );
                           if(!empty($fileExtention)){
                            $a[]=FULL_BASE_URL . $this->webroot . 'files'.DS.'postphoto'.DS.$pos;
                            if($width[$k])
                            $widt[] = $width[$k];
                            else $widt[] = 0;
                            if($height[$k])
                            $higt[] = $height[$k]; 
                            else $higt[] = 0; 
                           }else{
                            $a[]= "";
                            $widt[] = "";
                            $higt[] = "";
                           }
                           $k++; 
                       }
                       $pub[$i]['Post']['photo']=$a;
                       $pub[$i]['Post']['thumbnail'] = array();
                       $pub[$i]['Post']['width']=$widt; 
                       $pub[$i]['Post']['height']=$higt;
                    }
                    
                }
                if(empty($pubs['Post']['type'])){
                  $pub[$i]['Post']['photo']=array();
                  $pub[$i]['Post']['thumbnail'] = array();
                  $pub[$i]['Post']['width']= array(); 
                  $pub[$i]['Post']['height']= array();
                }  
                
                $ref = $this->User->find('first',array('conditions'=>array('User.id'=>$pubs['Post']['ref_id']),'fields'=>array('User.firstname','User.lastname'),'recursive'=>-1));
                if(!empty($ref)){    
                    $pub[$i]['Post']['ref_username'] = trim($ref['User']['firstname']." ".$ref['User']['lastname']);
                }else{
                    $pub[$i]['Post']['ref_username'] = '';
                }
                $arr = '';
                $arr .= strip_tags($pubs['Post']['post'])." ";
                 preg_match('~<iframe.*?src=["\']+(.*?)["\']+~', $pubs['Post']['post'], $urls);
                 if(sizeof($urls) >= 1){
                   $arr .=  $urls[1];
                 }  
             $pub[$i]['Post']['post'] = $arr;
            $pub[$i]['Post']['created'] = $this->Date->time_elapsed_string($pubs['Post']['created']);
            if ($pub[$i]['User']['image']) {
                $pub[$i]['User']['image'] = FULL_BASE_URL . $this->webroot . 'files/profile/' . $pubs['User']['image'];
            }else{
            $pub[$i]['User']['image']=FULL_BASE_URL .$this->webroot."inner".DS."images".DS."default-user-icon-profile.png";    
            }
            $lik=sizeof($pubs['Like']);
            if(!empty($lik)){
                $likpcm['like_list'] = $pubs['Like'];
                $pub[$i]['Like']= $likpcm;
                $pub[$i]['Like']['boolean']=1;
            }else{
                $likpcm['like_list'] = array();
                $pub[$i]['Like']= $likpcm;
                $pub[$i]['Like']['boolean']=0;
            }
            $lik=sizeof($pubs['Comment']);
            if(!empty($lik)){
                $ii= 0;
                foreach ($pubs['Comment'] as $mypo) {
                    $pubs['Comment'][$ii]['comment'] = htmlentities($mypo['comment']);
                    $ii++;
                }
               $com['comment_list'] = $pubs['Comment'];
                $pub[$i]['Comment']= $com;
                $pub[$i]['Comment']['boolean']=1;
            }else{
                $com['comment_list'] = array();
                $pub[$i]['Comment']= $com;
                $pub[$i]['Comment']['boolean']=0;
            }
            $pub[$i]['likecount']=sizeof($pubs['Like']);
            $pub[$i]['commentcount']=sizeof($pubs['Comment']);
                $i++;
             
            }
           // debug($pub); exit;
        $response['list']=$pub;
        $response['error']=1;
        $response['msg']="All public post";
     }else{
        $response['error']=0;
        $response['msg']="no public post";
     } 
        $this->set('response', $response);
        
        $this->render('ajax');
    } 
    public function publicpost($userid=NULL,$pagi=null){
        configure::write('debug',0);
        $this->layout="ajax";
        $this->loadModel('User');
        $this->loadModel('Post');
        $this->loadModel('Support');
        $this->loadModel('Share');
        $this->loadModel('ShareWith');
        $this->loadModel('Comment');
        $this->loadModel('Like');
        $pub=$this->Post->find('all',array('contain'=>array('User'=>array('fields'=>array('User.id','User.firstname','User.lastname','User.email','User.image')),'Like','ShareWith'=>array('fields'=>array('ShareWith.id','ShareWith.firstname','ShareWith.lastname','ShareWith.email','ShareWith.image')),'Comment'),'order'=>array('Post.id'=>'DESC'),'limit'=>10,'offset' =>$pagi));
        if(!empty($pub)){
            $i=0;
            $com=""; //debug($pub); exit;
            foreach ($pub as $pubs) { 
                $k = 0; $widt = array(); $higt = array();
                if($pubs['Post']['type']=="image"){
                    if(!empty($pubs['Post']['photo'])){
                       $po=unserialize($pubs['Post']['photo']); 
                       $width = unserialize($pubs['Post']['width']);
                       $height = unserialize($pubs['Post']['height']);
                       // print_r($po); exit;
                       $a=array(); 
                       foreach ($po as $pos) {
                           $fileExtention = pathinfo ( $pos, PATHINFO_EXTENSION );
                           if(!empty($fileExtention)){
                            $a[]=FULL_BASE_URL . $this->webroot . 'files'.DS.'postphoto'.DS.$pos;
                            if($width[$k])
                            $widt[] = $width[$k];
                            else $widt[] = 0;
                            if($height[$k])
                            $higt[] = $height[$k]; 
                            else $higt[] = 0;
                        }else{
                            $a[]= "";
                            $widt[] = "";
                            $higt[] = "";
                        } 
                       }
                       $pub[$i]['Post']['photo']=$a;
                       $pub[$i]['Post']['width']=$widt; 
                       $pub[$i]['Post']['height']=$higt;
                       $pub[$i]['Post']['thumbnail'] = array();
                    }
                    
                }
                if($pubs['Post']['type']=="video"){
                    if(!empty($pubs['Post']['photo'])){
                       $po=unserialize($pubs['Post']['photo']); 
                       $width = unserialize($pubs['Post']['width']);
                       $height = unserialize($pubs['Post']['height']);
                       // print_r($po); exit;
                       $a=array();
                       foreach ($po as $pos) {
                           $fileExtention = pathinfo ( $pos, PATHINFO_EXTENSION );
                           if(!empty($fileExtention)){
                           $a[]=FULL_BASE_URL . $this->webroot . 'files'.DS.'postphoto'.DS.$pos;
                           if($width[$k])
                            $widt[] = $width[$k];
                            else $widt[] = 0;
                            if($height[$k])
                            $higt[] = $height[$k]; 
                            else $higt[] = 0; 
                           }else{
                            $a[]= "";
                            $widt[] = "";
                            $higt[] = "";
                        }
                       }
                       $pub[$i]['Post']['photo']=$a;
                       $pub[$i]['Post']['width']=$widt; 
                       $pub[$i]['Post']['height']=$higt;
                       $pub[$i]['Post']['thumbnail'] = array();
                    }
                    
                }
                if(empty($pubs['Post']['type'])){
                  $pub[$i]['Post']['photo']= array();
                  $pub[$i]['Post']['width']= array(); 
                  $pub[$i]['Post']['height']= array();
                  $pub[$i]['Post']['thumbnail'] = array();
                } 
            $ref = $this->User->find('first',array('conditions'=>array('User.id'=>$pubs['Post']['ref_id']),'fields'=>array('User.firstname','User.lastname'),'recursive'=>-1));
                if(!empty($ref)){    
                    $pub[$i]['Post']['ref_username'] = trim($ref['User']['firstname']." ".$ref['User']['lastname']);
                }else{
                    $pub[$i]['Post']['ref_username'] = '';
                } 
                $arr = '';
                $arr .= strip_tags($pubs['Post']['post'])." ";
                 preg_match('~<iframe.*?src=["\']+(.*?)["\']+~', $pubs['Post']['post'], $urls);
                 if(sizeof($urls) >= 1){
                   $arr .=  $urls[1];
                 }  
             $pub[$i]['Post']['post'] = $arr;   
            // $pub[$i]['Post']['post'] = htmlentities($pubs['Post']['post']);
            $pub[$i]['Post']['created'] = $this->Date->time_elapsed_string($pubs['Post']['created']);
            if ($pub[$i]['User']['image']) {
                $pub[$i]['User']['image'] = FULL_BASE_URL . $this->webroot . 'files/profile/' . $pubs['User']['image'];
            }else{
            $pub[$i]['User']['image']= FULL_BASE_URL .$this->webroot."inner".DS."images".DS."default-user-icon-profile.png";    
            }
            $lik=sizeof($pubs['Like']);
            if(!empty($lik)){
                $likpcm['like_list'] = $pubs['Like'];
                $pub[$i]['Like']= $likpcm;
                $pub[$i]['Like']['boolean']=1;
            }else{
                $likpcm['like_list'] = array();
                $pub[$i]['Like']= $likpcm;
                $pub[$i]['Like']['boolean']=0;
            }
            $lik=sizeof($pubs['Comment']);
            if(!empty($lik)){
                $co = 0;
                foreach ($pubs['Comment'] as $comm) { 
                    if (!empty($comm['user_img'])) {
                      $pubs['Comment'][$co]['user_img'] = FULL_BASE_URL . $this->webroot . 'files/profile/' .$comm['user_img'];
                  }else{
                       $pubs['Comment'][$co]['user_img'] = FULL_BASE_URL .$this->webroot."inner".DS."images".DS."default-user-icon-profile.png"; 
                  }
                  $pubs['Comment'][$co]['comment'] = htmlentities($comm['comment']);
                    $cuser = $this->User->find('first',array('conditions'=>array('User.id'=>$comm['user_id']),'fields'=>array('User.firstname','User.lastname'),'recursive'=>0));
                  $pubs['Comment'][$co]['firstname'] = $cuser['User']['firstname'];
                  $pubs['Comment'][$co]['lastname'] = $cuser['User']['lastname'];
                  $co++; 
                } 
               $com['comment_list'] = $pubs['Comment'];
                $pub[$i]['Comment']= $com;
                $pub[$i]['Comment']['boolean']=1;
            }else{
                $com['comment_list'] = array();
                $pub[$i]['Comment']= $com;
                $pub[$i]['Comment']['boolean']=0;
            }
            $pub[$i]['likecount']=sizeof($pubs['Like']);
            $pub[$i]['commentcount']=sizeof($pubs['Comment']);
                $i++;
             
            }
           // debug($pub); exit;
        $response['list']=$pub;
        $response['error']=1;
        $response['msg']="All public post";
     }else{
        $response['error']=0;
        $response['msg']="no public post";
     } 
        $this->set('response', $response);
        
        $this->render('ajax');
    }
    public function isupport($usesid=NULL, $position= null){
        Configure::write('debug', 0);
      $post_sid = Array(); 
        $this->layout = "ajax";
        $this->loadModel('Support');
        $this->loadModel('Comment');
        $this->loadModel('Share');
        $this->loadModel('Like');
        $this->loadModel('User');
        if($usesid){
        $loginUser = $usesid;
        if (isset($usesid)) {
            $userId = $usesid;
        } else {
            $userId = $usesid;
        }
      $arr1[] = 1;
      $fid[] = 1; 
      $supporting = false;
      $_POST['support']=1;
      if( isset( $_POST['support'] ) &&  $_POST['support'] === "1" )
      {
        // am I supporting viewed user?
        
        $supportlist = $this->Support->find('all', array('conditions' => array(
                'AND' => array('Support.touserid' => $loginUser, 'Support.status' => 0, 'Support.block' => 0),
            ), 'fields' => array('Support.user_id'), 'recursive' => -1));
        if ($supportlist) {
            foreach ($supportlist as $supportlists) {
                if ($userId == $supportlists['Support']['user_id']) {
                    $supporting = true;
                }
            }
        }
        if ($userId == $loginUser) { // if is viewing own profile
            // users I support
            $supportlist = $this->Support->find('all', array('conditions' => array(
                    'AND' => array('Support.user_id' => $userId, 'Support.status' => 0, 'Support.block' => 0),
                ), 'fields' => array('Support.touserid'), 'recursive' => -1));
            if ($supportlist) {
                foreach ($supportlist as $supportlists) {
                    $arr1[] = $supportlists['Support']['touserid'];
                }
            }
         }
        } 

        $allusr = $this->User->find('all', array('conditions' => array('AND' => array('User.id' => $arr1)), 'fields' => array('User.id'), 'recursive' => 0));
        foreach ($allusr as $allusrs) {
            $fid[] = $allusrs['User']['id'];
        }
        $_POST['record_per_page']=5;
        $_POST["group_no"]=$position;
        // $_POST["group_no"]=0;
        $items_per_group = isset($_POST['record_per_page']) ? $_POST['record_per_page'] : 5; 
        $group_number = (int) $position;
        // $group_number = (int) $_POST["group_no"];

        $issameSendJust_supported = $this->Session->read('lastGroupNumber_supported');
       $increment = 0;
        if( $group_number === $issameSendJust_supported ){
          $increment = 1;
        }


         /* $position = ( ( $group_number + $increment ) * $items_per_group);
        if (empty($position)) {
            setcookie("welcomepost", "0", time() - 3600);
        } */

        $this->Session->write('lastGroupNumber_supported', $issameSendJust_supported);

        /*--------For Likes The Post------------------*/
      $post_support = [];
  $only_post_support  =  $this->Like->find('all',array(
                'fields'=>array('post_id'),
                'contain'=>false,'conditions'=>array(
                  'user_id'=>$usesid
                )));

                



            if( count( $only_post_support ) > 0 ) {
                   
                    foreach ($only_post_support as $key => $value) {
                        $post_support[] = $value['Like']['post_id'];
                    }
            }
    /*-----_For likes the post----------------------*/

    $this->loadModel('Post');
        $count1 = $this->Post->find('count', array(
            'conditions' => array(
                'OR' => array(
                              'AND' => array(
                                'Post.user_id' => $fid 
                               // 'Post.status' => '1'
                                ),
                              'Post.id' => $post_support 
                )),
            'contain' => array(
                'User' => array(
                    'fields' => array('User.id', 'User.firstname', 'User.firstname', 'User.lastname', 'User.image', 'User.sex','User.pciv_status')
                ),
                'Comment.User' => array(
                    'fields' => array('User.id', 'User.image')
                ),
                'ShareWith' => array(
                    'fields' => array('ShareWith.id', 'ShareWith.firstname', 'ShareWith.firstname', 'ShareWith.lastname', 'ShareWith.image', 'ShareWith.pciv_status')
                ),
                'Like',
                'Share'
                
            ),
            'order' => array('Post.id DESC'),
            'recursive' => 1,
        ));
        $other=array(0,$usesid);

        $result = $this->Post->find('all', array(
            'conditions' => array(
                'OR'=>array(
                    "AND"=>array(
                        'Post.share_with = Post.user_id',
                        'Post.share_with'=>$fid
                    ),
                    
                    'AND' => array(
                        'OR'=>array(
                            'Post.user_id' => $fid,
                            'Post.share_with' => $usesid,
                            //'Post.share_with ==' => 'Post.user_id'
                        ),
                        'Post.share_with' => $other
                       // 'Post.status' => '1'
                    ),
                    'Post.id' => $post_support
                )
                ),
           // 'fields' => array('Post.user_id' == 'Post.share_with'),
            'contain' => array(
                'User' => array(
                    'fields' => array('User.id', 'User.firstname', 'User.firstname', 'User.lastname', 'User.image','User.sex', 'User.pciv_status')
                ),
                'Comment.User' => array(
                    'fields' => array('User.id', 'User.image','User.firstname')
                ),
                'ShareWith' => array(
                    'fields' => array('ShareWith.id', 'ShareWith.firstname', 'ShareWith.firstname', 'ShareWith.lastname', 'ShareWith.image', 'ShareWith.pciv_status')
                ),
                'Like',
                'Share'
            ),
            'order' => array('Post.id DESC'),
            'limit' => $items_per_group,
            'offset' => $position,
            'recursive' => 1,
        ));
        $nextPageAvailable = $count1 > ($items_per_group + $position) ? true : false;
        
        $resu = array();
        foreach ($result as $results) {
        $k = 0; $widt = array(); $higt = array();          
            // debug($results['Share']);
            if ($results['User']['image']) {
                $results['User']['image'] = FULL_BASE_URL . $this->webroot . 'files/profile/' . $results['User']['image'];
            }
            $results['Post']['created'] = $this->Date->time_elapsed_string($results['Post']['created']);
            if($results['Post']['type']=='image'){
                $photo= unserialize($results['Post']['photo']);
                $width = unserialize($results['Post']['width']);
                $height = unserialize($results['Post']['height']); 
                if(!empty($photo)){
                    foreach($photo as $photos){
                        $pho=array();
                        $pho[]=FULL_BASE_URL .$this->webroot."files".DS."postphoto".DS.$photos;
                        if($width[$k])
                        $widt[] = $width[$k];
                        else $widt[] = 0;
                        if($height[$k])
                        $higt[] = $height[$k]; 
                        else $higt[] = 0;
                    }
            $results['Post']['photo'] = $pho; 
            $results['Post']['width']=$widt; 
            $results['Post']['height']=$higt; 
            $results['Post']['thumbnail'] = array(); 
            }
          }
          if($results['Post']['type']=='video'){
                $photo= unserialize($results['Post']['photo']);
                $width = unserialize($results['Post']['width']);
                $height = unserialize($results['Post']['height']); 
                if(!empty($photo)){
                    foreach($photo as $photos){
                        $pho=array();
                        $pho[]=FULL_BASE_URL .$this->webroot."files".DS."postphoto".DS.$photos;
                        if($width[$k])
                        $widt[] = $width[$k];
                        else $widt[] = 0;
                        if($height[$k])
                        $higt[] = $height[$k]; 
                        else $higt[] = 0;
                    }
            $results['Post']['photo'] = $pho; 
            $results['Post']['width']=$widt; 
            $results['Post']['height']=$higt;  
            $results['Post']['thumbnail'] = array();
            }
          } 
         if($results['Post']['type']==''){ 
            $results['Post']['photo'] = array(); 
            $results['Post']['width']= array(); 
            $results['Post']['height']= array();  
            $results['Post']['thumbnail'] = array();  
          } 
          if(sizeof($results['Like'])!=0){
            $lk=$this->Like->find('first',array('conditions'=>array('AND'=>array('Like.user_id'=>$usesid,'Like.post_id'=>$results['Post']['id'])),'fields'=>array('Like.id','Like.user_id','Like.post_id'),'order' => array('Like.id' => 'desc'),'recursive'=>-1));
            if(!empty($lk)){ 
            $results['Like']=$lk;
            $results['Like']['boolean']=1;
            }else{
             $results['Like']='';   
            $results['Like']['boolean']=0;
            }
        }else{
            $results['Like']='';
            $results['Like']['boolean']=0;
        }
            //$time = strtotime("$created");
            //$results['Post']['created']=$this->Timezone->Timezone_ctime($time);
                 $arr = '';
                $arr .= strip_tags($results['Post']['post'])." ";
                 preg_match('~<iframe.*?src=["\']+(.*?)["\']+~', $results['Post']['post'], $urls);
                 if(sizeof($urls) >= 1){
                   $arr .=  $urls[1];
                 }  
             $results['Post']['post'] = $arr; 
            // $results['Post']['post']=htmlentities($results['Post']['post']);
            if ($results['Post']['ref_id'] != '0') {
                $tu = $this->User->find('first', array('conditions' => array('User.id' => $results['Post']['ref_id']), 'fields' => array('firstname', 'lastname'), 'recursive' => 0));
//                debug($tu);
                $results['Post']['refername'] = ucfirst($tu['User']['firstname']) . " " . ucfirst($tu['User']['lastname']);
            }
            if ($results['Like']) {
                $results['Post']['likecount'] = $this->Like->find('count',array('conditions'=>array('Like.post_id'=>$results['Post']['id'])));
            } else {
                $results['Post']['likecount'] = 0;
            }
            if ($results['Comment']) {
                $results['Post']['commentcount'] = sizeof($results['Comment']);
            } else {
                $results['Post']['commentcount'] = 0;
            }
            if (!empty($results['ShareWith']['id'])) {
            if ($results['ShareWith']['image']) {
                $results['ShareWith']['image'] = FULL_BASE_URL . $this->webroot . 'files/profile/' . $results['ShareWith']['image'];
            }else{
            $results['ShareWith']['image']=FULL_BASE_URL .$this->webroot."inner".DS."images".DS."default-user-icon-profile.png";    
            }}
             $this->loadModel('PostView');
            $all_data=$this->PostView->find('count',array('conditions' => array('PostView.post_id' => $results['Post']['id'])));
              
             
                $results['Post']['view_count'] =$all_data;
            
            
           $comment_count = count($results['Comment']);
           for($i=0; $i<$comment_count; $i++){
               $created = $results['Comment'][$i]['created'];
                $time = strtotime($created);
                // $results['Comment'][$i]['created'] = $this->Timezone->Timezone_ctime($time);
                 $results['Comment'][$i]['created'] = $time; //$this->getTimeDiffrence($time);
                 $results['Comment'][$i]['comment'] = htmlentities($results['Comment'][$i]['comment']);
                 if($results['Comment'][$i]['User']['image']){
                 $results['Comment'][$i]['User']['image'] = FULL_BASE_URL . $this->webroot . 'files/profile/' . $results['Comment'][$i]['User']['image'];
              }else{
                $results['Comment'][$i]['User']['image'] =FULL_BASE_URL .$this->webroot."inner".DS."images".DS."default-user-icon-profile.png";    
              }
           }

            //debug($cmnt);
            foreach ($results['Share'] as $share) {
                $post_sid[] .= $share['post_id'];
                $shared_post = $this->Post->find('first', array('conditions' => array('Post.id' => $post_sid), 'recursive' => 1));
                $shares[] = $shared_post;
            }
            $resu[] = $results;
        }
        $res = array();
        if ($supporting || $userId == $loginUser) {
            $count2 = $this->Share->find('count', array('conditions' => array('OR' => array('Share.user_id' => $userId, 'Share.share_with' => $userId))));
            $qery = $this->Share->find('all', array('conditions' => array('OR' => array('Share.user_id' => $userId, 'Share.share_with' => $userId)), 'limit' => $items_per_group, 'offset' => $position));
            if (!$nextPageAvailable) {
                $nextPageAvailable1 = $count2 > ($position + $items_per_group) ? true : false;
            }
// debug($results); exit;
            // $quer =  $this->User->find('all', array('conditions' => array('User.id' => $userId)));
            foreach ($qery as $q) {
                // debug($results['Share']);
                if ($q['User']['image']) {
                    $q['User']['image'] = FULL_BASE_URL . $this->webroot . 'files/profile/' . $q['User']['image'];
                }
                $q['Post']['created'] = $this->Date->time_elapsed_string($q['Post']['created']);
               // $time = strtotime("$created");
                //$q['Post']['created']=$this->Timezone->Timezone_ctime($time);
                if ($q['Post']['ref_id'] != '0') {
                    $tu = $this->User->find('first', array('conditions' => array('User.id' => $q['Post']['ref_id']), 'fields' => array('firstname', 'lastname'), 'recursive' => 0));
                    //                debug($tu);
                    $q['Post']['refername'] = $tu['User']['firstname'] . " " . $tu['User']['lastname'];
                }
                if ($q['Like']) {
                    $q['Post']['likecount'] = $this->Like->find('count',array('conditions'=>array('Like.post_id'=>$q['Post']['id'])));
                } else {
                    $q['Post']['likecount'] = 0;
                }
                if ($q['Comment']) {
                    $q['Post']['commentcount'] = sizeof($q['Comment']);
                } else {
                    $q['Post']['commentcount'] = 0;
                }
                
                $comment_count = count($q['Comment']);
                for($i=0; $i<$comment_count; $i++){
                    $created = $q['Comment'][$i]['created'];
                    $time = strtotime($created);
                    $q['Comment'][$i]['created'] = $this->getTimeDiffrence($time);
                }
                $res[] = $q;
            }
        }
        $response['list']=$resu;
        $response['error']=0;
      $response['msg']="No post found";
     }else{
      $response['error']=0;
      $response['msg']="No post found";
     }
        $this->set('response',$response);
     //    debug($resu);
      // exit;
      $this->render("ajax");
    }
    public function suppoter_unsuppoter($userid=null,$postid=null){
     configure::write('debug',0);
        $this->layout="ajax";
        $this->loadModel('Like');
        $this->loadModel('Notification');
        $this->loadModel('Post'); 
        $this->loadModel('User'); 
        if(isset($userid)&&isset($postid)){
        $link=$this->Like->find('first',array('conditions'=>array('AND'=>array('Like.post_id'=>$postid,'Like.user_id'=>$userid)),'recursive'=>-1));
        $this->request->data['Like']['user_id']=$userid;
        $this->request->data['Like']['post_id']=$postid; 
        $user = $this->User->find('first',array('conditions'=>array('User.id'=>$userid), fields=>array('device_token'), 'recursive'=>-1));
        $user['User']['device_token'] = 'AAAAXRxemkk:APA91bF0GtmmiBfLCpACI-Ajd51mwM1yW4BvwxEPrjuEq3TQxwd48Ge_ZKglY9jl51sO0yD3fwL5uyPwedlQwjvLHNRXvXdOZg70OIxZc1gSh1h9pMtTm8D-pD0svYJqbQSqoN5w0iiP';
        if(!$link){  
            $uid=$this->Post->find('first',array('conditions'=>array('Post.id'=>$postid),'fields'=>array('Post.user_id','Post.type')));
           $this->Like->save($this->request->data);
           $this->request->data['Notification']['touserid']=$uid['Post']['user_id'];
            $this->request->data['Notification']['post_id']= $postid;
            $this->request->data['Notification']['user_id']= $userid;
            $this->request->data['Notification']['type']=0;
            $this->request->data['Notification']['status']=0; 
             $this->Notification->save($this->request->data); 
             $data = array
                (
                    'message'   => 'You Support',
                    'title'     => 'Support',
                    'subtitle'  => 'This is a subtitle. subtitle',
                    'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
                    'vibrate'   => 1,
                    'sound'     => 1,
                    'largeIcon' => 'large_icon',
                    'smallIcon' => 'small_icon'
                );
            $not = $this->pushFcmNotification($user['User']['device_token'],$data);
           $response['msg']="Support success";  
        }else{
        $this->Like->delete($link['Like']['id']); 
        $data = array
                (
                    'message'   => 'You Unsupport',
                    'title'     => 'Unsupport',
                    'subtitle'  => 'This is a subtitle. subtitle',
                    'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
                    'vibrate'   => 1,
                    'sound'     => 1,
                    'largeIcon' => 'large_icon',
                    'smallIcon' => 'small_icon'
                );
            $not = $this->pushFcmNotification($user['User']['device_token'],$data);
        $response['msg']="Unsupport success";
        }
        $response['error']=1;
        $response['count']=$this->Like->find('count',array('conditions'=>array('Like.post_id'=>$postid),'recursive'=>-1));
        $lik=$this->Like->find('first',array('conditions'=>array('AND'=>array('Like.post_id'=>$postid,'Like.user_id'=>$userid)),'fields'=>array('Like.id','Like.user_id','Like.post_id'),'recursive'=>-1)); 
        if($lik){
         $response['like']=$lik;   
         $response['like']['boolean']=1; 
         $response['like']['notification']=$not; 
        }else{
         $response['like']['boolean']=0;
         $response['like']['notification']=$not;     
        } 
        }else{
         $response['msg']="Invalid user id or post id";   
         $response['error']=0; 
        }
        $this->set('response',$response);
        $this->render('ajax');   
    }
    public function supp_unsupp($userid=null,$postid=null,$id=null){
        configure::write('debug',0);
        $this->layout="ajax";
        $this->loadModel('Like');
        if(!empty($id)){
         $delid=$this->Like->find('first',array('conditions'=>array('Like.id'=>$id),'fields'=>'Like.id','recursive'=>0)); 
        if($id==$delid['Like']['id']){ 
            if($this->Like->delete($id)){
                $del=$this->Like->find('count',array('conditions'=>array('Like.post_id'=>$postid),'fields'=>array('Like.id','Like.user_id','Like.post_id'),'recursive'=>-1));
                $likde=$this->Like->find('first',array('conditions'=>array('AND'=>array('Like.post_id'=>$postid,'Like.user_id'=>$userid)),'fields'=>array('Like.id','Like.user_id','Like.post_id'),'order' => array('Like.id' => 'desc'),'recursive'=>-1));
                $as=$likde;
                if(empty($likde)){
                    $likde['Like']['id']="";
                    $likde['Like']['user_id']="";
                    $likde['Like']['post_id']=""; 
                } 
                $response['count']=$del;
                $response['like']=$likde;
                if(!empty($as)){
                    $response['like']['boolean']=1; 
                }else{
                    $response['like']['boolean']=0; 
                } 
                $response['error']=1;
                $response['msg']="Count list"; 
            }
        }else{
            $del=$this->Like->find('count',array('conditions'=>array('Like.post_id'=>$postid),'fields'=>array('Like.id','Like.user_id','Like.post_id'),'recursive'=>-1));
            $likde=$this->Like->find('first',array('conditions'=>array('AND'=>array('Like.post_id'=>$postid,'Like.user_id'=>$userid)),'fields'=>array('Like.id','Like.user_id','Like.post_id'),'order' => array('Like.id' => 'desc'),'recursive'=>-1));
            $as=$likde;
            if(empty($likde)){
                    $likde['Like']['id']="";
                    $likde['Like']['user_id']="";
                    $likde['Like']['post_id']=""; 
                    $response['like']['boolean']=0;
                }else{
                    $response['like']['boolean']=1;
                }  
                $response['count']=$del;
                $response['like']=$likde;
                if(!empty($as)){
                    $response['like']['boolean']=1; 
                }else{
                    $response['like']['boolean']=0; 
                } 
                $response['error']=1;
                $response['msg']="Count list";  
        }   
        }
        else{ 
            $this->request->data['Like']['user_id']=$userid;
            $this->request->data['Like']['post_id']=$postid;
            $this->request->data['Like']['status']=0; 
            if($this->Like->save($this->request->data)){
            $del=$this->Like->find('count',array('conditions'=>array('Like.post_id'=>$postid),'fields'=>array('Like.id','Like.user_id','Like.post_id'),'recursive'=>-1));
            $likde=$this->Like->find('first',array('conditions'=>array('AND'=>array('Like.post_id'=>$postid,'Like.user_id'=>$userid)),'fields'=>array('Like.id','Like.user_id','Like.post_id'),'order' => array('Like.id' => 'desc'),'recursive'=>-1));
            $as=$likde;
            if(empty($likde)){
                    $likde['Like']['id']="";
                    $likde['Like']['user_id']="";
                    $likde['Like']['post_id']="";
                    $res['boolean']=0;
                }else{
                    $res['boolean']=1;
                }
        }
        $response['count']=$del;
        $response['like']=$likde;
        if(!empty($as)){
            $response['like']['boolean']=1; 
        }else{
            $response['like']['boolean']=0; 
        } 
        $response['error']=1;
        $response['msg']="Count list"; 
               
        }
        $this->set('response',$response);
        $this->render('ajax');
    }
    public function commentlist($postid=null,$userid=null){
        configure::write('debug',0);
        $this->layout="ajax";
        $this->loadModel('Comment');
       $this->loadModel('User');
        $com=$this->request->data['Comment']['comment'];
        if($com){
            $usr=$this->User->find('first',array("conditions"=>array('User.id'=>$userid),'fields'=>'User.image','recursive'=>-1));
            $this->request->data['Comment']['post_id']=$postid;
            $this->request->data['Comment']['user_id']=$userid;
            $this->request->data['Comment']['email_notification']=0;
            $this->request->data['Comment']['status']=0;
            $this->request->data['Comment']['user_img']=$usr['User']['image'];
            $this->Comment->save($this->request->data);
            $id=$this->Comment->getLastInsertID();
            $this->Comment->query('UPDATE `comments` SET `email_notification`="0" WHERE `id`='.$id);
            $comm1=$this->Comment->find('first',array('conditions'=>array('AND'=>array('Comment.id'=>$id,'Comment.user_id'=>$userid,'Comment.post_id'=>$postid)),'recursive'=>-1));
            $usr=$this->User->find('first',array('conditions'=>array('User.id'=>$comm1['Comment']['user_id']),'fields'=>array('User.firstname','User.lastname'),'recursive'=>-1));
                   $comm1['Comment']['firstname']=$usr['User']['firstname']; 
                   $comm1['Comment']['lastname']=$usr['User']['lastname']; 
            if($comm1['Comment']['user_img']){
                $comm1['Comment']['user_img']=FULL_BASE_URL .$this->webroot."files".DS.'profile'.DS.$comm1['Comment']['user_img'];
                 }else{
                  $comm1['Comment']['user_img']=FULL_BASE_URL .$this->webroot."inner".DS."images".DS."default-user-icon-profile.png";  
                 }
                 $comm1['Comment']['created'] = $this->Date->time_elapsed_string($comm1['Comment']['created']); 
            $response['list']=$comm1;
            $response['error']=1;
            $response['msg']="last comment"; 
        }else{
             $comm=$this->Comment->find('all',array('conditions'=>array('Comment.post_id'=>$postid),'recursive'=>-1));
             if($comm){
                $i=0; 
                foreach($comm as $comms){
                    $arr = '';
                    $usr=$this->User->find('first',array('conditions'=>array('User.id'=>$comms['Comment']['user_id']),'fields'=>array('User.firstname','User.lastname'),'recursive'=>-1));
                   $comm[$i]['Comment']['firstname']=$usr['User']['firstname']; 
                   $comm[$i]['Comment']['lastname'] = $usr['User']['lastname']; 
                   preg_match_all('~<img.*?src=["\']+(.*?)["\']+~', $comms['Comment']['comment'], $urls);
                      $arr .= " ".strip_tags($comms['Comment']['comment']);
                     if(sizeof($urls[1]) >= 1){
                        foreach ($urls[1] as $urlimg) {
                            $arr .= utf8_encode(FULL_BASE_URL .$this->webroot.$urlimg)." ";
                        }
                   } 
                    // debug($comms['Comment']['comment']);
                   
                  
                   $comm[$i]['Comment']['comment'] =  $arr;
                    if($comms['Comment']['user_img']){
                $comm[$i]['Comment']['user_img']=FULL_BASE_URL .$this->webroot."files".DS.'profile'.DS.$comms['Comment']['user_img'];
                 }else{
                  $comm[$i]['Comment']['user_img']=FULL_BASE_URL .$this->webroot."inner".DS."images".DS."default-user-icon-profile.png";  
                 } 
                 $comm[$i]['Comment']['created'] = $this->Date->time_elapsed_string($comms['Comment']['created']);
                 $i++;
                }
                $response['list']=$comm;
                $response['error']=1;
                $response['msg']="Comment List"; 
             }else{ 
            $response['error']=0;
            $response['msg']="no Comment List";
             }  
        }
        $this->set('response',$response);
        $this->render('ajax');
    }
  /*  public function public_post($iserid=null){
        configure::write('debug',2);
        $this->layout="ajax";
         $public_profile_user = $this->User->find('all', array('fields'=>array('id'),'contain'=>false,'conditions' => array(
                                 'AND'=>array(
                                 'User.profile_status' => 0,
                                 'User.id <>'=>$iserid
                                 )
                              ), 'order' => array('User.id' => 'DESC')));
       /*  if ( $public_profile_user && count( $public_profile_user ) > 0 ) {
                foreach ($public_profile_user as $public_profile) {
                    $arr1[] = $public_profile['User']['id'];
                }
            }
            $arr1[] = $iserid; */

             /* ===========list all friend id============== */
       /* $allusr = $this->User->find('all', array('conditions' => array('AND' => array('User.id IN' => $arr1)), 'fields' => array('User.id'), 'recursive' => 0));
        foreach ($allusr as $allusrs) {
            $fid[] = $allusrs['User']['id'];
        }

         debug($fid); * /

        $this->render('ajax');
    } */
    public function emooji(){
        configure::write('debug',0);
        $this->layout="ajax"; 
        for($i=1; $i <= 70; $i++){
            $im=FULL_BASE_URL .$this->webroot."img".DS."emoticons".DS.$i.".png";
            // $res[] = htmlentities('<img class="emocome" src='.$im.'>');
            $res[] = $im;
        } 
        $response['list']=$res;
        $response['error']=1;
        $response['msg']="Emoji list";
        $this->set('response',$response);
        $this->render('ajax'); 
    }
    public function post_edit($postid=null){
        configure::write('debug',0); 
        $this->layout="ajax"; 
        $this->loadModel('Post');
        $this->Post->id=$postid;
        // $post=@$this->request->data['Post']['post']; 
            if (!empty($postid)) {
                $this->Post->save($this->request->data);
                $poo=$this->Post->find('first',array('conditions'=>array('Post.id'=>$postid),'recursive'=>-1)); 
                $response['error']=1;
                $response['list']=$poo;
                $response['msg']="Post Updated"; 
        }else{
            $response['error']=0;
            $response['msg']="Post not Updated";
        }
        $this->set('response',$response);
        $this->render('ajax'); 
    }
    public function post_delete($postid=null){
        configure::write('debug',0); 
        $this->layout="ajax"; 
        $this->loadModel('Post'); 
        $this->Post->id=$postid;
        $ts=$this->Post->find('first',array('conditions'=>array('Post.id'=>$postid),'fields'=>'Post.id','recursive'=>-1));
         
        if (!empty($ts)) { 
            $this->Post->delete();
            $response['error']=1;
            $response['msg']="Post Deleted";
        }else{
          $response['error']=0;
          $response['msg']="Invalid Post id";  
        }
        $this->set('response',$response);
        $this->render('ajax'); 
    }
    public function about(){
            configure::write('debug',0); 
            $this->layout="ajax";  
            $this->loadModel('Staticpage');
            $data=$this->Staticpage->find('first',array('conditions'=>array('AND'=>array('Staticpage.position'=>'about','Staticpage.status'=>1)))); 
            if($data){
             $data['Staticpage']['description']=htmlspecialchars($data['Staticpage']['description']);
             $response['list']= $data;
             $response['error']= 1;
             $response['message']= "About us page";
            }else{
             $response['error']= 0;
             $response['message']= "Not found";   
            }
            $this->set('response',$response);
            $this->render('ajax');
        }
        public function edit_profile($id = NULL) {
        configure::write('debug',0); 
        $this->layout="ajax";  
        $this->loadModel('User');
        $this->User->id = $id;
        if (!$this->User->exists()) {
         $response['error']=0; 
         $response['message']="Sorry No User found";   
        }else{
        if ($this->request->is('post') || $this->request->is('put')) { 
            $cntry = isset($this->request->data['User']['country']) ? $this->request->data['User']['country'] : '';
            // $cnt = $this->Location->find('first', array('conditions' => array('Location.name' => $cntry)));
            $this->request->data['User']['country'] = $cntry; // $cnt['Location']['name'];

            if (isset($this->request->data['User']['state']) && $this->request->data['User']['state'] != '') {
                $stat = $this->request->data['User']['state'];
                // $st = $this->Location->find('first', array('conditions' => array('Location.location_id' => $stat)));
                $this->request->data['User']['state'] = $stat; //$st['Location']['name'];
            }
            if (isset($this->request->data['User']['city']) && $this->request->data['User']['city'] != '') {
                $cty = $this->request->data['User']['city'];
                // $ct = $this->Location->find('first', array('conditions' => array('Location.location_id' => $cty)));
                $this->request->data['User']['city'] = $cty; //$ct['Location']['name'];
            }
             $this->request->data['User']['lastname'] = "";
            if ($this->User->save($this->request->data)) {
                $response['message']="Successfully Updated"; 
                // $this->Session->setFlash(__('The User has been saved'));
                // $this->redirect(array('action' => 'edit_profile/' . $id));
            } else {
                $response['message']="The User could not be saved. Please, try again."; 
                // $this->Session->setFlash(__('The User could not be saved. Please, try again.'));
                // $this->redirect(array('action' => 'edit_profile/' . $id));
            }
           $response['error']=1; 
        } else {
            $this->request->data = $this->User->read(null, $id);
            $response['error']=1;
           $response['message']="Update Failed"; 
        }
       $usr = $this->User->find('first', array('conditions' => array('User.id' => $id),'fields'=>array('User.id','User.firstname','User.email','User.image','User.original_image','User.coverphoto','User.country','User.state','User.city','User.phone','User.cell_no','User.home','User.sex','User.dob','User.born_address','User.raised_child','User.where_live','User.where_lived','User.where_visited','User.family_member','User.relation_ship','User.workplace_company','User.addict_alco_type','User.addtimezone','User.time_addict_alco','User.highschool','User.college','User.company','User.position','User.designation','User.city_town','User.color','User.workplace_description','User.professional_skill','User.college_passout','User.highschool_passout','User.current_city_town','User.yourhometown','User.your_places','User.meeting','User.about_you'),'recursive'=>-1));
           if($usr){
           $fileExtention = pathinfo ( $usr['User']['image'], PATHINFO_EXTENSION );
            if($fileExtention):
            $usr['User']['image']= FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$usr['User']['image'];
            else:
            $usr['User']['image']= FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
            endif;
            $fileExtention = pathinfo ( $usr['User']['coverphoto'], PATHINFO_EXTENSION );
            if($fileExtention):
            $usr['User']['coverphoto']= FULL_BASE_URL . $this->webroot ."files".DS."coverphoto".DS.$usr['User']['coverphoto'];
            else:
            $usr['User']['coverphoto']= FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."cover_photo1.jpg";
            endif;
             
        }
       // debug($usr); exit;
       $response['list']=$usr;
     }
        $this->set('response',$response);
        $this->render('ajax');
        
    }
    public function help($id=NULL) {
        configure::write('debug',2); 
        $this->layout="ajax";  
        if ($this->request->is('post')) {
            $this->loadModel('Help');
            $this->Help->create();
            if ($this->Help->save($this->request->data)) {
                $Projecturl = 'http://mysponsers.com/m/';
                $message1 = $this->request->data['Help']['description'];              
                $type = $this->request->data['Help']['type'];
                $subject = "Help Send messages";
                $from = $this->request->data['Help']['email'];
                
                $message = "Dear Manager, we have received help request from application user. <br><br><strong>Contact From: {$from}</strong><br><br><strong>Type Of Isses :</strong> $type<br><br><strong>Your Message :</strong> $message1 <br><br><strong><a href='$Projecturl'>www.mysponsers.com/</a></strong>";
                try{
                 $this->Datamail->custom_mail("contact@mysponsers.com", "$subject", $message); 
                }catch(Exception $e){

                }
                
                // $this->Session->setFlash(__('Your message have been send,We will help you soon, Thanks'));
                $message2 = "Hello, we have received your help request, we will reply very soon. We apologize for the inconvenience, but we promise, your issue(s) will be resolved immediately!";
                $message = "$message2 <br><br><strong>Contact From: {$from}</strong><br><br><strong>Type Of Isses :</strong> $type<br><br><strong>Your Message :</strong> $message1 <br><br><strong><a href='$Projecturl'>www.mysponsers.com/</a></strong>";
                try{
                 $this->Datamail->custom_mail($this->request->data['Help']['email'], $subject, $message);   
                }catch(Exception $e){

                }
                
                $response['error']='Your message have been send,We will help you soon, Thanks';
                $response['message']=1;
                // return $this->redirect(array('action' => 'index'));
            } else {
                // $this->Session->setFlash(__('Your help message could not be send. Please, try again.'));
                $response['error']='Your help message could not be send. Please, try again.';
                $response['message']=0;
            }
        }else{
            $loggedid = $id;
        $dataAbout = $this->User->find('first', array('conditions' => array('User.id' => $loggedid),'fields'=>array('User.id','User.firstname','User.email','User.image','User.original_image','User.coverphoto','User.country','User.state','User.city','User.phone','User.cell_no','User.home','User.sex','User.dob','User.born_address','User.raised_child','User.where_live','User.where_lived','User.where_visited','User.family_member','User.relation_ship','User.workplace_company','User.addict_alco_type','User.addtimezone','User.time_addict_alco','User.highschool','User.college','User.company','User.position','User.designation','User.city_town','User.color','User.workplace_description','User.professional_skill','User.college_passout','User.highschool_passout','User.current_city_town','User.yourhometown','User.your_places','User.meeting','User.about_you'),'recursive'=>-1));
        if($dataAbout){
           $fileExtention = pathinfo ( $dataAbout['User']['image'], PATHINFO_EXTENSION );
            if($fileExtention):
            $dataAbout['User']['image']=FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$dataAbout['User']['image'];
            else:
            $dataAbout['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
            endif;
            $fileExtention = pathinfo ( $dataAbout['User']['coverphoto'], PATHINFO_EXTENSION );
            if($fileExtention):
            $dataAbout['User']['coverphoto']=FULL_BASE_URL . $this->webroot ."files".DS."coverphoto".DS.$dataAbout['User']['coverphoto'];
            else:
            $dataAbout['User']['coverphoto']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."cover_photo1.jpg";
            endif;
        }
        // $this->set('userAbout', $dataAbout); 
        // debug($dataAbout); exit;
        $response['list']=$dataAbout;
        $response['error']='No input found';
        $response['message']=0;
        }
        $this->set('response',$response);
        $this->render('ajax');
    }
    public function photo($id=NULL) {
        configure::write('debug',0); 
        $this->layout="ajax";  
        $this->loadModel('Post');
        $lid = $id;
        $dataImage = $this->Post->find('all', array(
            'contain' => false,
            'fields' => array(
                     'Post.id',
                    'Post.photo'
                ),
            'conditions' => array(
                'AND' => array(
                    'Post.user_id' => $lid
                ),
                'OR' => array(
                    'Post.type' => array('image')
                )
            )
        ));  //debug($dataImage); exit;
        if($dataImage){
            foreach($dataImage as $dataImages){
             if($dataImages['Post']['photo']){
                $pho=unserialize($dataImages['Post']['photo']);
                $a=array();
                foreach ($pho as $phos) {
                   $filename =  FULL_BASE_URL . $this->webroot."files".DS.'postphoto'.DS.'original'.DS.$phos;  
                   $filename1 = FULL_BASE_URL . $this->webroot."files".DS.'postphoto'.DS.$phos;  
                    if (@getimagesize($filename)) {
                       $a[]= FULL_BASE_URL . $this->webroot.'files'.DS.'postphoto'.DS.'original'.DS.$phos; 
                    } elseif(@getimagesize($filename1)) {
                       $a[]= FULL_BASE_URL . $this->webroot.'files'.DS.'postphoto'.DS.$phos; 
                    }else{}     
                }
                if(!empty($a)){
                    $dataImages1['Post']['id'] = $dataImages['Post']['id'];
                    $dataImages1['Post']['photo'] = $a; 
                    $dsa[]=$dataImages1;
                } 
            }   
            
            }
            $response['pdata']['list']=$dsa;
            $response['pdata']['error']=1;
            $response['pdata']['msg']="Post photo list"; 
        }else{
           $response['pdata']['list']=array(); 
           $response['pdata']['error']=0;
           $response['pdata']['msg']="No Post photo found";  
        }   
        // $this->set('pdata', $dataImage); 
        $dataVideo = $this->Post->find('all', array(
            'contain' => false,
            'fields' => array(
                     'Post.id',
                    'Post.photo'
                ),
            'conditions' => array(
                'AND' => array(
                    'Post.user_id' => $lid
                ),
                'OR' => array(
                    'Post.type' => array('video')
                )
            )
        ));
        // debug($dataVideo); exit;
        if($dataVideo){
            foreach($dataVideo as $dataVideos){ 
             if($dataVideos['Post']['photo']){
                $pho=unserialize($dataVideos['Post']['photo']);
                $a=array();  
                foreach ($pho as $phos) {  
                   $filename =  FULL_BASE_URL . $this->webroot."files".DS.'postphoto'.DS.'original'.DS.$phos; 
                   $filename1 =  FULL_BASE_URL . $this->webroot.'files'.DS.'postphoto'.DS.$phos;   
                   //  if (@getimagesize($filename)) {
                   //     $a[]= FULL_BASE_URL . $this->webroot.'files'.DS.'postphoto'.DS.'original'.DS.$phos; 
                   //  } elseif(@getimagesize($filename1)) {
                   //      debug($filename1);
                       $a[]= FULL_BASE_URL . $this->webroot.'files'.DS.'postphoto'.DS.$phos; 
                    // }else{}    exit;
                } 
                if(!empty($a)){
                     $dataVideos1['Post']['id'] = $dataVideos['Post']['id'];
                     $dataVideos1['Post']['photo'] = $a;
                     $dsaa[]=$dataVideos1;
                }
                
            }    
            }
            if(!empty($dsaa)){
                $response['video']['list']=$dsaa;
                $response['video']['error']=1;
                $response['video']['msg']="Video list";
            }else{
                $response['video']['list']=array();
                $response['video']['error']=0;
                $response['video']['msg']="No video found"; 
            }
            // debug($response); exit; 
        }else{
           $response['video']['list']=array();
           $response['video']['error']=0;
           $response['video']['msg']="No video found";  
        }  
        // $this->set('videoData', $dataVideo); 

        $this->loadModel("Gallery");
        $photos = $this->Gallery->find("all",array(

            'contain' => false,
            'fields' => array(
                    'Gallery.id',
                    'Gallery.image'
                ),
            "conditions"=>array(
                "AND"=>array(
                    'Gallery.user_id'=>$lid,
                    'Gallery.type'=>"profile"
                )
            )
        ));  
        if($photos){
            foreach($photos as $photoss){ 
                if($photoss['Gallery']['image']){  
                    // $filename = FULL_BASE_URL. $this->webroot."files/coverphoto/original".DS.$photoss['Gallery']['image']; 
                     $filename = FULL_BASE_URL. $this->webroot."files/profile".DS.$photoss['Gallery']['image'];  
                   $filename1 = FULL_BASE_URL. $this->webroot."files/profile".DS.'original'.DS.$photoss['Gallery']['image']; 
                   // debug($filename1);  
                    if (@getimagesize($filename1)) {
                        $photoss['Gallery']['image']= FULL_BASE_URL . $this->webroot.'files'.DS.'profile'.DS.'original'.DS.$photoss['Gallery']['image'];   
                    } elseif(@getimagesize($filename)) {
                        $photoss['Gallery']['image']= FULL_BASE_URL . $this->webroot.'files'.DS.'profile'.DS.$photoss['Gallery']['image'];  
                    }else{ $photoss['Gallery']['image']=""; }   
                } 
             if($photoss['Post']['photo']){
                $pho=unserialize($photoss['Post']['photo']);
                $a=array();
                foreach ($pho as $phos) {
                    $filename = FULL_BASE_URL . $this->webroot.DS.'files'.DS.'postphoto'.DS.'original'.DS.$phos;  
                    $filename1 = FULL_BASE_URL . $this->webroot.DS.'files'.DS.'postphoto'.DS.$phos; 
                    if (@getimagesize($filename)) {
                       $a[]= FULL_BASE_URL . $this->webroot.'files'.DS.'postphoto'.DS.'original'.DS.$phos; 
                    } elseif(@getimagesize($filename1)) {
                       $a[]= FULL_BASE_URL . $this->webroot.'files'.DS.'postphoto'.DS.$phos; 
                    }else{ }   
                }
                $photoss['Post']['photo']=$a;  
            }   
            $dsaaa[]=$photoss;
            } 
            $response['profile_photos']['list']=$dsaaa;
            $response['profile_photos']['error']=1;
            $response['profile_photos']['msg']="profile photo list"; 
        }else{
           $response['profile_photos']['list']= array(); 
           $response['profile_photos']['error']=0;
           $response['profile_photos']['msg']="No profile photo found";  
        }  
        // $this->set("profile_photos",$photos); 
        $landscape_photos = $this->Gallery->find("all",array( 
                           'contain' => false,
                            'fields' => array(
                                    'Gallery.id',
                                    'Gallery.image'
                                ),
                                "conditions"=>array(
                                    "AND"=>array(
                                        'Gallery.user_id'=>$lid,
                                        'Gallery.type'=>"landscape"
                                    )
                                )
                            ));
        if($landscape_photos){ 
            $a =[];
            foreach($landscape_photos as $landscape_photoss){
                if($landscape_photoss['Gallery']['image']){ 
                   $filename = FULL_BASE_URL . $this->webroot."files".DS."coverphoto".DS."original".DS.$landscape_photoss['Gallery']['image']; 
                   $filename1 = FULL_BASE_URL . $this->webroot."files".DS."coverphoto".DS.$landscape_photoss['Gallery']['image'];
                 if (@getimagesize($filename)) {
                    $landscape_photoss['Gallery']['image']= FULL_BASE_URL . $this->webroot.'files'.DS.'coverphoto'.DS.'original'.DS.$landscape_photoss['Gallery']['image']; 
                } elseif(@getimagesize($filename1)){
                    $landscape_photoss['Gallery']['image']= FULL_BASE_URL . $this->webroot.'files'.DS.'coverphoto'.DS.$landscape_photoss['Gallery']['image'];
                }else {
                    $landscape_photoss['Gallery']['image']=""; }  
                     $aaa[] = $landscape_photoss; 
                    
                }

                 // debug($landscape_photos['Post']); exit;
             if($landscape_photoss['Post']['photo']){
                $pho=unserialize($landscape_photoss['Post']['photo']);
                debug($pho);
                $a=array();
                foreach ($pho as $phos) {
                    $filename =  FULL_BASE_URL . $this->webroot.'files'.DS.'postphoto'.DS.'original'.DS.$phos;  
                    $filename1 =  FULL_BASE_URL . $this->webroot.'files'.DS.'postphoto'.DS.$phos; 
                    if (@getimagesize($filename)) {
                       $a[]= FULL_BASE_URL . $this->webroot.'files'.DS.'postphoto'.DS.'original'.DS.$phos; 
                    } elseif(@getimagesize($filename1)) {
                       $a[]= FULL_BASE_URL . $this->webroot.'files'.DS.'postphoto'.DS.$phos; 
                    }else{} 
                } 
            }   
            if(!empty($a)){
                 $landscape_photoss1['Post']['id']= $landscape_photoss['Post']['id'];   
                 $landscape_photoss1['Post']['photo']=$a; 
                 $aaa[]=$landscape_photoss1;
            }
            }
            if(!empty($aaa)){
              $response['landscape_photos']['list']=$aaa;
              $response['landscape_photos']['error']=1;
              $response['landscape_photos']['msg']="landscape photo list";  
            }else{
               $response['landscape_photos']['list']= array(); 
               $response['landscape_photos']['error']=0;
               $response['landscape_photos']['msg']="No landscape photo found";  
            }    
             
        }else{
           $response['landscape_photos']['list']= array(); 
           $response['landscape_photos']['error']=0;
           $response['landscape_photos']['msg']="No landscape photo found";  
        } 
         //debug($aaa); exit;
        // $this->set("landscape_photos",$landscape_photos);
        $this->set('response',$response);
        $this->render('ajax');  
    }
    public function livemeetinglist(){
        configure::write('debug',0); 
        $this->layout="ajax";  
        $this->loadModel('Meeting');
// load UserMeeting Model
            $this->loadModel('UserMeeting');
// load Online Model
            $this->loadModel('Online');

            $meetings= [];
            $meetings = $this->Meeting->find('all');
            
            $online_user = $this->Online->find("all",array(
                    'fields'=>array('Online.user_id')
                )  );

            $i=0;
            if($meetings){
            foreach($meetings as $meeting){

              $id = $meeting['Meeting']['id'];

              $livechat_count = $this->UserMeeting->find('all',

                      array('conditions' => array(

                          'AND'=>array(

                              'UserMeeting.meeting_id' => $id,

                              'UserMeeting.status' =>1

                          )

                                          ),

                                'fields'=>array('DISTINCT UserMeeting.user_id')

                            ));

              $online_meeting = array();
              foreach($livechat_count as $meeting_user){
                    foreach ($online_user as $online){
                        if($meeting_user['UserMeeting']['user_id']==$online['Online']['user_id']){
                            $online_meeting[]= $meeting_user['UserMeeting']['user_id'];
                        }
                    }
                }
                $online_meeting = array_unique($online_meeting);
                $users = $this->User->find("all",array('conditions'=>array('OR'=>array(
                    'User.id'=>$online_meeting
                )),
                    'recursive' => 1,
                    'fields'=>array('User.id','User.firstname','User.lastname','User.image'),
                    'contain'=>false
                    ));
              $asd =  count($users);   

              $meetings[$i]['UserMeeting']=$users;

              $meetings[$i]['Meeting']['cnt'] = $asd;

              $i++;

            }
         $response['list']= $meetings;
         $response['error']= 1;  
         $response['msg']= "Live Meeting list";  
        }else{
         $response['error']= 0;  
         $response['msg']= "No Live Meeting list";    
        }

            // $this->set('meetings',$meetings);
            $this->set('response',$response);
        $this->render('ajax');  
    }
    public function livemeeting($id = null,$uid=null){
            configure::write('debug',2); 
            $this->layout="ajax";  
            $this->loadModel('Meeting');
            if (!$this->Meeting->exists($id)) {
              $response['error']=0;
              $response['msg']="Invalid id";
             }else{
                $meeting_detail = $this->Meeting->find('first',array(
                    'conditions'=>array(
                        'id' => $id
                    )
                ));
                if($meeting_detail){
                   $response['meeting_detail']=$meeting_detail; 
                }else{
                    $response['meeting_detail']['id']="";
                }
                // debug($meeting_detail); exit;
                // $this->set('meeting_detail',$meeting_detail);
                $this->loadModel('Livechat');
                $this->loadModel('UserMeeting');
        if ($this->request->is('post')) {
            $this->request->data['Livechat']['meeting_id']=$id; 
            $this->request->data['Livechat']['user_id']=$uid;        
            $this->Livechat->create();
            if ($this->Livechat->save($this->request->data)) {
                $this->Session->setFlash(__('The meeting has been saved.'));
                // return $this->redirect(array('action' => 'index'));
            } else {
                // $this->Session->setFlash(__('The meeting could not be saved. Please, try again.'));
            }
        }
                $options = array('conditions' => array('meeting_id'  => $id));
                $live_chat = $this->Livechat->find('all', $options);
            if($live_chat){
                foreach($live_chat as $live_chats){
                    // debug($live_chats['Livechat']['chat']); exit;
                    if($live_chats['Livechat']){
                    $live_chats['Livechat']['chat']=htmlentities($live_chats['Livechat']['chat']);
                    } 
                  $fileExtention = pathinfo ( $live_chats['User']['image'], PATHINFO_EXTENSION );
                    if($fileExtention):
                    $live_chats['User']['image']=FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$live_chats['User']['image'];
                    else:
                    $live_chats['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                    endif;
                    $fileExtention = pathinfo ( $live_chats['User']['coverphoto'], PATHINFO_EXTENSION );
                    if($fileExtention):
                    $live_chats['User']['coverphoto']=FULL_BASE_URL . $this->webroot ."files".DS."coverphoto".DS.$live_chats['User']['coverphoto'];
                    else:
                    $live_chats['User']['coverphoto']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."cover_photo1.jpg";
                    endif;  
                    $li_chat[]=$live_chats;
                }
           
          }
          if($li_chat){
            $response['livechat']=$li_chat;
          }else{
            $response['livechat']="";
          }
                // debug($li_chat); exit;
        // $this->set('livechat', $live_chat);
                
                $meeting_chat_count = $this->Livechat->find('all',array('conditions' => array('Livechat.meeting_id' => $id),'fields'=>array('DISTINCT Livechat.user_id')));
                if($meeting_chat_count){
                    $response['meetingCount12']=$meeting_chat_count;
                }else{
                    $response['meetingCount12']="";
                }
                // debug($response); exit;
                // $this->set('meetingCount12', $meeting_chat_count);
                
                $livechat_count = $this->UserMeeting->find('all',
                      array('conditions' => array(
                          'AND'=>array(
                              'UserMeeting.meeting_id' => $id,
                              'UserMeeting.status' =>1
                          )
                                          ),
                                'fields'=>array('DISTINCT UserMeeting.user_id')
                            ));
                if($livechat_count){
                    $response['meetingCount']=$livechat_count;
                }else{
                    $response['meetingCount']="";
                }
                
                // $this->set('meetingCount', $livechat_count);
                
                $this->loadModel('User');
                $total_meeting_user_old = $this->Livechat->find('all',array('conditions' => array('Livechat.meeting_id' => $id),
                 'fields'=>array('DISTINCT Livechat.user_id','User.id','User.firstname','User.lastname','User.image')));
                
                $this->loadModel('UserMeeting');
                $total_meeting_user = $this->UserMeeting->find("all",array('conditions'=>array(
                    "AND"=>array(
                        'UserMeeting.meeting_id' => $id,
                        'UserMeeting.status'=>1
                    )
                        ),
                    'fields'=>array('DISTINCT UserMeeting.user_id','User.id','User.firstname','User.lastname','User.image')
                    ));
//                debug($total_meeting_user);
                
                $this->loadModel('Online');
                $online_user = $this->Online->find("all",array(
                    'fields'=>array('Online.user_id')
                )  
                        );
                $online_meeting=array();
                if($total_meeting_user){
                foreach($total_meeting_user as $meeting_user){
                    foreach ($online_user as $online){
                        if($meeting_user['UserMeeting']['user_id']==$online['Online']['user_id']){
                            $online_meeting[]= $meeting_user['UserMeeting']['user_id'];
                        }
                    }
                }
                } 
//                debug($online_meeting); 
                $users = $this->User->find("all",array('conditions'=>array('OR'=>array(
                    'User.id'=>$online_meeting
                )),
                    'recursive' => -1,
                    'fields'=>array('User.id','User.firstname','User.lastname','User.image')
                    ));
//                debug($users);
                if($users){
                    $response['totalMeeting']=$users;
                }else{
                    $response['totalMeeting']="";
                }
                // debug($response); exit;
                // $this->set('totalMeeting',$users);
                $response['error']=1;
                $response['msg']="All meeting list by meeting id";
            }
            $this->set('response',$response);
            $this->render('ajax'); 
        }
    public function supports($userid=null) {
    configure::write('debug',0); 
    $this->layout="ajax";  
    $this->loadModel('User'); 
    $this->loadModel('Support');
    $this->Support->recursive=0;

    $suppoting=$this->Support->find('all',array('conditions'=>array('Support.user_id'=>$userid),'fields'=>array('Support.touserid'))); 
    if($suppoting){
        foreach($suppoting as $invitelists_ing){
            $email_ing[]= $invitelists_ing['Support']['touserid'];
         }
//       debug($email);
        $em1=  sizeof($email_ing);
        if($em1==1)$email_ing[]=$userid;               
        if($email_ing){
          $allusring=$this->User->find('all',array(
                'conditions'=>array('User.id IN'=>$email_ing),
                'fields'=>array('User.id','User.firstname','User.lastname','User.image','User.email'),
                'contain'=>array('Support'=>array('conditions'=>array('Support.user_id'=>$userid),
                'fields'=>array('Support.id','Support.user_id','Support.touserid','Support.block','Support.created'))),
                'recursive'=>1)
                ); 
          
          $allusr_recent_suppoting=$this->User->find('all',array('conditions'=>array('User.id IN'=>$email_ing),'fields'=>array('User.id','User.firstname','User.lastname','User.image','User.email'),
            'contain'=>array('Support'=>array('conditions'=>array('Support.user_id'=>$userid),
                'fields'=>array('Support.id','Support.user_id','Support.touserid','Support.block','Support.created'))),'order'=>"User.id Desc",'recursive'=>1,'limit'=>6,));
          
          foreach($allusring as $allusrs_ing){
           $mutualing['Mutualing'][$allusrs_ing['User']['id']]=$this->Support->find('count',array('conditions'=>array('OR'=>array('Support.user_id'=>$allusrs_ing['User']['id'],'Support.touserid'=>$allusrs_ing['User']['id'])),'recursive'=>-1));
            }
        
        } 
    }
        
    $invitelist=$this->Support->find('all',array('conditions'=>array('Support.touserid'=>$userid),'fields'=>array('Support.user_id')));
    // debug($allusring);
    if($invitelist){
        foreach($invitelist as $invitelists){
            $email[]= $invitelists['Support']['user_id'];
        }
//        debug($email); 
        $em=  sizeof($email);
        if($em==1){ $email[]=$userid; }
         // debug($em);
        if($email){
            $allusr=$this->User->find('all',array('conditions'=>array('User.id IN'=>$email),'fields'=>array('User.id','User.firstname','User.lastname','User.image','User.email'),
              'contain'=>array('Support'=>array('conditions'=>array('Support.touserid'=>$userid),
              'fields'=>array('Support.id','Support.user_id','Support.touserid','Support.block' ))),'recursive'=>1));                  

            $allusr_recent_suppoter=$this->User->find('all',array('conditions'=>array('User.id IN'=>$email),'fields'=>array('User.id','User.firstname','User.lastname','User.image','User.email'),
              'contain'=>array('Support'=>array('conditions'=>array('Support.user_id'=>$userid),
              'fields'=>array('Support.id','Support.user_id','Support.touserid','Support.block' ))),'order'=>"User.id Desc",'recursive'=>1));

            /* remove
            foreach($allusr as $allusrs){
                $mutual['Mutual'][$allusrs['User']['id']]=$this->Support->find('count',array('conditions'=>array('OR'=>array('Support.user_id'=>$allusrs['User']['id'],'Support.touserid'=>$allusrs['User']['id'])),'recursive'=>-1));
            } */
            
        $allusr_recent=$this->User->find('all',array('conditions'=>array('User.id IN'=>$email),'fields'=>array('User.id','User.firstname','User.lastname','User.image'),
            'contain'=>array('Support'=>array('conditions'=>array('Support.user_id'=>$userid),
                'fields'=>array('Support.id','Support.user_id','Support.touserid'))),'order'=>"User.id Desc",'recursive'=>1)); 
//         debug($allusr_recent);       
/*        $allusr_block=$this->User->find('all',array('conditions'=>array(array('User.id IN'=>$email)),'fields'=>array('User.id','User.firstname','User.lastname','User.image'),
                'contain'=>array('Support'=>array('conditions'=>array('AND'=>array('Support.touserid'=>$userid,'Support.block'=>1)),
            'fields'=>array('Support.id','Support.user_id','Support.touserid','Support.block'))),'order'=>"User.id Desc",'recursive'=>1));*/ //debug($userid); debug($email);
            $allusr_block=$this->User->find('all',array('conditions'=>array('User.id IN'=>$email),'fields'=>array('User.id','User.firstname','User.lastname','User.image','User.email'),
              'contain'=>array('Support'=>array('conditions'=>array('Support.touserid'=>$this->Auth->User('id')),
              'fields'=>array('Support.id','Support.user_id','Support.touserid','Support.block' ))),'recursive'=>1));
        
       // debug($allusr_block); exit;
            if($allusr_block){
                foreach($allusr_block as $allusr_blocks){
                    if(!empty($allusr_blocks['Support'])){
                        $block_mutual['Mutual'][$allusr_blocks['User']['id']]=$this->Support->find('count',array('conditions'=>array('OR'=>array('Support.user_id'=>$allusr_blocks['User']['id'],'Support.touserid'=>$allusr_blocks['User']['id'])),'recursive'=>-1)); 
                    }
                    }
            }

            $d= $this->Support->find('all',array('conditions'=>array('Support.touserid'=>$userid)));
            foreach($d as $us){
                $uid[]=$us['Support']['user_id'];
            }
//            debug($uid);
            $count_uid = count($uid);
            if($count_uid == 1){
                $dus= $this->User->find('all',array('conditions'=>array('User.id'=>$uid[0])));
                $dusprt= $this->Support->find('all',array('conditions'=>array('Support.user_id'=>$uid[0])));
                $blkd= $this->User->find('all',array('conditions'=>array('User.id'=>$uid[0])));
                $bldusprt= $this->Support->find('all',array('conditions'=>array('Support.user_id'=>$uid[0])));
            }else{
                // debug("$count_uid.else");
                $dus= $this->User->find('all',array('conditions'=>array('User.id IN'=>$uid)));
                $dusprt= $this->Support->find('all',array('conditions'=>array('Support.user_id IN'=>$uid)));
                $blkd= $this->User->find('all',array('conditions'=>array('User.id IN'=>$uid)));
                $bldusprt= $this->Support->find('all',array('conditions'=>array('Support.user_id IN'=>$uid)));
            }
//            debug($dus); 
//           debug($dusprt); 
           //$dus= $this->User->find('all',array('conditions'=>array('User.id IN'=>$uid)));
          // $dusprt= $this->Support->find('all',array('conditions'=>array('Support.user_id IN'=>$uid)));
//           $blkd= $this->User->find('all',array('conditions'=>array('User.id IN'=>$uid)));
//           $bldusprt= $this->Support->find('all',array('conditions'=>array('Support.user_id IN'=>$uid)));
      
           $ni=count($dusprt);
          for($i=0;$i<$ni;$i++){        
            $dus[$i]['Support'][0]=$dusprt[$i]['Support'];
          }
          for($i=0;$i<$ni;$i++){    
            $blkd[$i]['Support'][0]=$bldusprt[$i]['Support'];
          }
      
           $allusr['myownusr']=$dus;
           $allusr_block['myblock']=$blkd;
             
        }
    }
    // debug($allusr);exit;
    if(isset($allusr)){
        $prs = array();
        foreach ($allusr as $allusrs) { 
            if($allusrs['User']['id']){
             $fileExtention = pathinfo ( $allusrs['User']['image'], PATHINFO_EXTENSION );
                    if($fileExtention):
                    $allusrs['User']['image']=FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$allusrs['User']['image'];
                    else:
                    $allusrs['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                    endif;
                    $tf=$this->Support->find("first",array('conditions'=>array('AND'=>array('Support.user_id'=>$allusrs['User'],'Support.touserid'=>$userid)),'recursive'=>-1));
                    $allusrs['User']['support_from'] = date("F d, Y", strtotime($tf['Support']['created'])); 
                    $allusrs['User']['mutualsupporter'] = $this->Support->find('count',array('conditions'=>array('OR'=>array('Support.user_id'=>$allusrs['User']['id'],'Support.touserid'=>$allusrs['User']['id'])),'recursive'=>-1));
             
                    $prs[]=$allusrs;
                }
                    
        }
        $list['list_allusr'] = $prs;
        $response['allusr']= $list;
        $response['allusr']['msg']="All  users list";
        $response['allusr']['error']=1;
    }else{
        $list['list_allusr'] = array();
        $response['allusr']= $list;
        $response['allusr']['msg']="No  users list";
        $response['allusr']['error']=0;
    }
    /*if(isset($mutual)){
        $mu['list_mutual'] = $mutual;
        $response['mutual']= $mu;
        $response['mutual']['msg']="All  mutual users list";
        $response['mutual']['error']=1;
    }else{
        $mu['list_mutual'] = array();
        $response['mutual']= $mu;
        $response['mutual']['msg']="No  mutual user";
        $response['mutual']['error']=0;
    } */
    if(isset($allusr_recent)){ 
        $recent11=array();
        foreach ($allusr_recent as $allusr_recents) { 
            if($allusr_recents['User']['id']){
             $fileExtention = pathinfo ( $allusr_recents['User']['image'], PATHINFO_EXTENSION );
                    if($fileExtention):
                    $allusr_recents['User']['image']=FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$allusr_recents['User']['image'];
                    else:
                    $allusr_recents['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                    endif; 
                    $tf=$this->Support->find("first",array('conditions'=>array('AND'=>array('Support.user_id'=>$allusr_recents['User'],'Support.touserid'=>$userid)),'recursive'=>-1));
                    $allusr_recents['User']['support_from'] = date("F d, Y", strtotime($tf['Support']['created'])); 
                    $allusr_recents['User']['mutualsupporter'] = $this->Support->find('count',array('conditions'=>array('OR'=>array('Support.user_id'=>$allusr_recents['User']['id'],'Support.touserid'=>$allusr_recents['User']['id'])),'recursive'=>-1));
                     $recent11[]=$allusr_recents; 
                }  
            } 
         $rem['list_allusr_recent'] = $recent11; 
        $response['allusr_recent']= $rem;
        $response['allusr_recent']['msg']="All  recent users list";
        $response['allusr_recent']['error']=1;
    }else{
        $rem['list_allusr_recent'] = array();
        $response['allusr_recent']= $rem;
        $response['allusr_recent']['msg']="No recent users list";
        $response['allusr_recent']['error']=0;
    }
    if(isset($allusr_block)){
        $recent1111=array();
        foreach ($allusr_block as $allusr_blocks) { 
            if($allusr_blocks['User']['id']){
             $fileExtention = pathinfo ( $allusr_blocks['User']['image'], PATHINFO_EXTENSION );
                    if($fileExtention):
                    $allusr_blocks['User']['image']=FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$allusr_blocks['User']['image'];
                    else:
                    $allusr_blocks['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                    endif;
                    $tf=$this->Support->find("first",array('conditions'=>array('AND'=>array('Support.user_id'=>$allusr_blocks['User'],'Support.touserid'=>$userid)),'recursive'=>-1));

                    $allusr_blocks['User']['support_from'] = date("F d, Y", strtotime($tf['Support']['created']));

                    $allusr_blocks['User']['mutualsupporter'] = $this->Support->find('count',array('conditions'=>array('OR'=>array('Support.user_id'=>$allusr_blocks['User']['id'],'Support.touserid'=>$allusr_blocks['User']['id'])),'recursive'=>-1));

                     $recent1111[]=$allusr_blocks;
                }
            } 
         $remp['list_allusr_recent'] = $recent1111; 
        $response['allusr_recent']= $remp;
        $response['allusr_recent']['msg']="All recent blocked user list";
        $response['allusr_recent']['error']=1;
    }else{
        $remp['list_allusr_recent'] = array(); 
        $response['allusr_recent']= $remp;
        $response['allusr_recent']['msg']="No recent blocked user list";
        $response['allusr_recent']['error']=0;
    } 
    if(isset($block_mutual)){ 
        $allBlockUserByThisUser = $this->Support->find('all',array('conditions'=>array('Support.touserid'=>$userid,'Support.block'=>1),'fields'=>array('Support.id','Support.user_id','Support.touserid','Support.block','Support.created'),'contain'=>array('User'=>array('fields'=>array('User.id','User.firstname','User.lastname','User.email','User.image')))));
        // debug($block_mutual); exit;
        $recent2=array();
        foreach ($allBlockUserByThisUser as $block_mutuals) { 
            if($block_mutuals['User']['id']){
             $fileExtention = pathinfo ( $block_mutuals['User']['image'], PATHINFO_EXTENSION );
                    if($fileExtention):
                    $block_mutuals['User']['image']=FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$block_mutuals['User']['image'];
                    else:
                    $block_mutuals['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                    endif;
                    $block_mutuals['User']['support_from'] = date("F d, Y", strtotime($block_mutuals['Support']['created']));
                    $block_mutuals['User']['mutualsupporter']=$this->Support->find('count',array('conditions'=>array('OR'=>array('Support.user_id'=>$block_mutuals['User']['id'],'Support.touserid'=>$block_mutuals['User']['id'])),'recursive'=>-1));
                    $block_mutuals['User']['isBlocked'] = $block_mutuals['Support']['block'];
                     $recent2[]=$block_mutuals;
                }
            }
        $bl['list_blocked_user'] = $recent2;   
        $response['blocked']= $bl;
        $response['blocked']['msg']="All blocked user list";
        $response['blocked']['error']=1;
    }else{
        $bl['list_blocked_user'] = array();   
        $response['blocked']= $bl;
        $response['blocked']['msg']="No blocked user list";
        $response['blocked']['error']=0;
    } 
    if(isset($allusr_recent_suppoter)){
        $recent21=array();
        foreach ($allusr_recent_suppoter as $allusr_recent_suppoters) { 
            if($allusr_recent_suppoters['User']['id']){
             $fileExtention = pathinfo ( $allusr_recent_suppoters['User']['image'], PATHINFO_EXTENSION );
                    if($fileExtention):
                    $allusr_recent_suppoters['User']['image']=FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$allusr_recent_suppoters['User']['image'];
                    else:
                    $allusr_recent_suppoters['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                    endif;

                    $tf=$this->Support->find("first",array('conditions'=>array('AND'=>array('Support.user_id'=>$allusr_recent_suppoters['User'],'Support.touserid'=>$userid)),'recursive'=>-1));

                    $allusr_recent_suppoters['User']['support_from'] = date("F d, Y", strtotime($tf['Support']['created']));

                    $allusr_recent_suppoters['User']['mutualsupporter'] = $this->Support->find('count',array('conditions'=>array('OR'=>array('Support.user_id'=>$allusr_recent_suppoters['User']['id'],'Support.touserid'=>$allusr_recent_suppoters['User']['id'])),'recursive'=>-1));

                     $recent21[]=$allusr_recent_suppoters;
                }
            }
         $r21['list_recent_user'] = $recent21; 
        $response['allusr_recent_suppoter']= $r21;
        $response['allusr_recent_suppoter']['msg']="All  user recent list";
        $response['allusr_recent_suppoter']['error']=1;
    }else{
         $r21['list_recent_user'] = array(); 
        $response['allusr_recent_suppoter']= $r21;
        $response['allusr_recent_suppoter']['msg']="No recent user list";
        $response['allusr_recent_suppoter']['error']=0;
    } 
    if(isset($mutualing)){
        $recent3=array();
        foreach ($mutualing as $mutualings) { 
            if($mutualings['User']['id']){
             $fileExtention = pathinfo ( $mutualings['User']['image'], PATHINFO_EXTENSION );
                    if($fileExtention):
                    $mutualings['User']['image']=FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$mutualings['User']['image'];
                    else:
                    $mutualings['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                    endif;
                     $recent3[]=$mutualings;
                }
            }
        $r3['list_mutualing'] = $recent3;    
        $response['mutualing']= $r3;
        $response['mutualing']['msg']="All mutual user list";
        $response['mutualing']['error']=1;
    }else{
        $r3['list_mutualing'] = array();    
        $response['mutualing']= $r3;
        $response['mutualing']['msg']="No mutual user found";
        $response['mutualing']['error']=0;
    } 
    if(isset($allusring)){
        $recent4=array();
        foreach ($allusring as $allusrings) { 
            if($allusrings['User']['id']){
             $fileExtention = pathinfo ( $allusrings['User']['image'], PATHINFO_EXTENSION );
                    if($fileExtention):
                    $allusrings['User']['image']=FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$allusrings['User']['image'];
                    else:
                    $allusrings['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                    endif;

                    $tf=$this->Support->find("first",array('conditions'=>array('AND'=>array('Support.user_id'=>$allusrings['User'],'Support.touserid'=>$userid)),'recursive'=>-1));

                    $allusrings['User']['support_from'] = date("F d, Y", strtotime($tf['Support']['created']));

                    $allusrings['User']['mutualsupporter'] = $this->Support->find('count',array('conditions'=>array('OR'=>array('Support.user_id'=>$allusrings['User']['id'],'Support.touserid'=>$allusrings['User']['id'])),'recursive'=>-1));

                     $recent4[]=$allusrings;
                }
            }
        if(!empty($recent4)){
            $gdp['list_allusring'] = $recent4;
        }else{
          $gdp['list_allusring'] = array();   
        }    
        $response['allusring']=$gdp;
        $response['allusring']['msg']="All user supporting";
        $response['allusring']['error']=1;
    }else{
        $gdp['list_allusring'] = array();
        $response['allusring']=$gdp;
        $response['allusring']['msg']="Not found user recent supporter";
        $response['allusring']['error']=0;
    } 
    if(isset($allusr_recent_suppoting)){
        $recent5=array();
        foreach ($allusr_recent_suppoting as $allusr_recent_suppotings) { 
            if($allusr_recent_suppotings['User']['id']){
             $fileExtention = pathinfo ( $allusr_recent_suppotings['User']['image'], PATHINFO_EXTENSION );
                    if($fileExtention):
                    $allusr_recent_suppotings['User']['image']=FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$allusr_recent_suppotings['User']['image'];
                    else:
                    $allusr_recent_suppotings['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                    endif;

                    $tf=$this->Support->find("first",array('conditions'=>array('AND'=>array('Support.user_id'=>$allusr_recent_suppotings['User'],'Support.touserid'=>$userid)),'recursive'=>-1));

                    $allusr_recent_suppotings['User']['support_from'] = date("F d, Y", strtotime($tf['Support']['created']));

                    $allusr_recent_suppotings['User']['mutualsupporter'] = $this->Support->find('count',array('conditions'=>array('OR'=>array('Support.user_id'=>$allusr_recent_suppotings['User']['id'],'Support.touserid'=>$allusr_recent_suppotings['User']['id'])),'recursive'=>-1));
                    
                     $recent5[]=$allusr_recent_suppotings;
                }
            }
        $r5['list_recent_support'] = $recent5;  
        $response['allusr_recent_suppoting']= $r5;
        $response['allusr_recent_suppoting']['msg']="All user recent supporter";
        $response['allusr_recent_suppoting']['error']=1; 
    }else{
        $r5['list_recent_support'] = array();  
        $response['allusr_recent_suppoting']= $r5;
        $response['allusr_recent_suppoting']['msg']="Not found user recent supporter";
        $response['allusr_recent_suppoting']['error']=0;
    }
    // debug($response); exit;
    $response['msg']="supporters";
    $response['error']=1;
    $this->set('response',$response);         
    $this->render('ajax');
}
public function onlinemember($id=NULL){
    configure::write("debug",0);
    $this->layout="ajax";
    $this->loadModel('Online');
    $this->loadModel('User');
    if (!$this->User->exists($id)) {
        $response['error']=0;
        $response['msg']="Invalid User";
     }else{ 
       // $totalmember = $this->User->find('count',array('conditions'=>array('User.status'=>'1'))); 
        $totalmember = $this->User->find('count'); 
      $online_user = $this->Online->find("all",array('conditions'=>array('NOT'=>array('Online.user_id'=>$id)),
        'contain'=>array('User'=>array('fields'=>array('User.id','User.firstname','User.lastname','User.email','User.image')))));
      if($online_user){
        $online=array();
        $online_userss = "";
        foreach ($online_user as $online_users) { 
            $online_userss['Online']['id'] = $online_users['Online']['id'];
            $online_userss['Online']['ip'] = $online_users['Online']['ip'];
            $online_userss['Online']['user_id'] = $online_users['Online']['user_id'];
            if($online_users['Online']['image']){
                 $fileExtention = pathinfo ( $online_users['Online']['image'], PATHINFO_EXTENSION );
                        if($fileExtention):
                        $online_userss['Online']['image']=FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$online_users['Online']['image'];
                        else:
                        $online_userss['Online']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                        endif; 
                    }else{
                        $online_userss['Online']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                    }
            $online_userss['Online']['url'] = $online_users['Online']['url'];
            $online_userss['Online']['modified'] = $online_users['Online']['modified'];
            $online_userss['Online']['firstname'] = $online_users['Online']['firstname'];
            $online_userss['Online']['lastname'] = $online_users['Online']['lastname'];  
            $online_userss['Online']['state'] = $online_users['Online']['state'];  
            $online_userss['Online']['city'] = $online_users['Online']['city'];        
               /* if($online_users['User']['image']){
                 $fileExtention = pathinfo ( $online_users['User']['image'], PATHINFO_EXTENSION );
                        if($fileExtention):
                        $online_users['User']['image']=FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$online_users['User']['image'];
                        else:
                        $online_users['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                        endif; 
                    }else{
                     $online_users['User']['image']=FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";   
                    } */
                    // $online_users['User']['status'] = "online";
                    $online[]=$online_userss;
                } 
                $response['list']=$online;
                $response['total_member'] = number_format($totalmember);
                $response['error']=1;
                $response['msg']="Online user list";
      }else{
        $response['total_member'] = number_format($totalmember);
        $response['error']=0;
        $response['msg']="No online user list found";
      }
     }
     $this->set('response',$response);
    $this->render('ajax');
} 
public function livechat_add($id=null,$userid=null) {
			configure::write("debug",2);
            $this->layout="ajax";  
			$this->loadModel('Livechat');
			$this->loadModel('User'); 
            //print_r($_POST);
		if ($this->request->is('post')) {
			$this->Livechat->create();
//                        $cdate=$this->request->data['Livechat']['created'];
//                        $date = date_create($cdate); 
//                       $this->request->data['Livechat']['created']=  date_format($date, 'F d, Y h:i A'); 
					  $this->request->data['Livechat']['meeting_id']=  $id; 
					  $this->request->data['Livechat']['user_id']=  $userid; 
                        $date=date('F d, Y h:i A');
                       $this->request->data['Livechat']['created']=  $date; 
                       $this->request->data['Livechat']['chat']= trim($this->request->data['Livechat']['chat']);
			if ($this->Livechat->save($this->request->data)) {
							$usr=$this->User->find("first",array('conditions'=>array("User.id"=>$userid),
																 'fields'=>array('User.firstname','User.lastname','User.image'),'recursive'=>-1));
							if($usr){ 
								$fileExtention = pathinfo ( $usr['User']['image'], PATHINFO_EXTENSION );
								if($fileExtention):
								$response['image'] =FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$usr['User']['image'];
								else:
								$response['image'] =FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
								endif; 
							}else{
								$response['image'] =FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
							}
                            $response['msg']='success';
                            $response['user']=  ucfirst($usr['User']['firstname'])." ".ucfirst($usr['User']['lastname']) ;
                            $response['chat']= trim($this->request->data['Livechat']['chat']);
                            $response['created']= $date;
                           // $response['image']=$this->Auth->user('image');
                            $response['id']=$userid;
							$response['error']=1;
			 } else {
                            $response['msg']='failure';
							$response['error']=0;
			}
                        //$response = json_encode($response); 
		}else{
			$response['msg']='No input found';
			$response['error']=0;
		}
		$this->set('response',$response);
        $this->render('ajax');
	} 
 public function autosearchtop() {
        $this->layout = "ajax";
        configure::write('debug', 0);
        
        $keyword = $this->request->data['keyword'];
        if(!empty($keyword)){ 
        $obj1 = $this->User->find('all', array('conditions' => array('OR' => array(
                    'User.firstname LIKE' => '%' . $keyword . '%',
                    'User.lastname LIKE' => '%' . $keyword . '%',
                    'User.email LIKE' => '%' . $keyword . '%',
                    'User.image LIKE' => '%' . $keyword . '%',
                    'User.created LIKE' => '%' . $keyword . '%',
                    'User.country LIKE' => '%' . $keyword . '%',
                    'User.state LIKE' => '%' . $keyword . '%',
                    'User.city LIKE' => '%' . $keyword . '%',
                    'User.yourhometown LIKE' => '%' . $keyword . '%',
        ),'AND'=> array('User.role'=>'User')),'fields'=>array('User.id','User.firstname','User.lastname','User.image','User.email'),'recursive'=>-1));
        if(!empty($obj1)){ 
            $resp  = array();
            foreach ($obj1 as $obj12) {
                $fileExtention = pathinfo ( $obj12['User']['image'], PATHINFO_EXTENSION ); 
                if($fileExtention):
                $obj12['User']['image'] =FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$obj12['User']['image'];
                else:
                $obj12['User']['image'] =FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                endif;  
              $resp[] = $obj12;                
            }
            $response['list'] = $resp;
            $response['error'] = 1;
            $response['msg'] = 'All Search list';
        }else{
            $response['error'] = 0;
            $response['msg'] = 'No Search list';
        }
    }else{
      $response['error'] = 0;
      $response['msg'] = 'inValid input';  
    }
       
        
       /* $obj1 = $this->User->find('all', array('conditions' => array('OR' => array(
                'concat_ws(" ", User.firstname, User.lastname) LIKE ' => '%'. $keyword . '%',
                'User.state LIKE' => '%' . $keyword . '%',
                'User.city LIKE' => '%' . $keyword . '%'
        ))));*/
        //   $obj1 = $this->User->find('all', array('contain'=>false,'fields'=>array('id','firstname','lastname','image'),'conditions' => array(
        //         'User.firstname LIKE ' => '%'. $keyword . '%'
        // )));
        $this->set('response', $response);
        $this->render('ajax');
    }   
    public function profileimg_save_to_file($userid=null){
        $this->layout = 'ajax';
        configure::write("debug",2);
        $this->loadModel('User');
        if ($this->request->is('post')) {
            $imagePath = WWW_ROOT . 'files/profile/';

            $allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
            $temp = explode(".", $_FILES["img"]["name"]);
            $image = $_FILES["img"]["name"];
            $image = str_replace(' ', '_', $image);
            $current_date =date("Ymdhis");

            $extension =pathinfo($image, PATHINFO_EXTENSION);
            $extension_old = end($temp);

            //Check write Access to Directory

            if(!is_writable($imagePath)){
                $response = Array(
                    "error" => '0',
                    "msg" => 'Can`t upload File; no write Access'
                );
               // print json_encode($response);
               // return;
            }
    
            if ( in_array($extension, $allowedExts)){
                if ($_FILES["img"]["error"] > 0){
                    $response = array(
                           "error" => '0',
                           "msg" => 'ERROR Return Code: '. $_FILES["img"]["error"],
                   );           
                }else{
                    $filename = $_FILES["img"]["tmp_name"];
                    list($width, $height) = getimagesize( $filename );
                    $image_name= $current_date.$image;
                    if(move_uploaded_file($filename,  $imagePath . $image_name)){
                        $this->User->id=$userid;
                        $this->User->saveField("original_image",$image_name);
                        $this->User->saveField("image",$image_name);
                        $this->loadModel('Gallery');
                        $this->request->data['Gallery']['image']=$image_name;
                        $this->request->data['Gallery']['user_id']=$userid;
                        $this->request->data['Gallery']['type']="profile";
                        $this->Gallery->create();
                        if($this->Gallery->save($this->request->data)){
                            $gallery="Data saved into gallery"; 
                        }else{
                            $gallery="Data did/'t saved into gallery";
                        }
                        
                    }
                    $imageUrl= "https://".$_SERVER['SERVER_NAME']. "/files/profile/";
                    $response = array(
                          "error" => 1,
                          "url" => $imageUrl.$image_name,
                          "width" => $width,
                          "height" => $height,
                          "image_save"=>$gallery
                    );
                }
            }
            else
            {
             $response = array(
                    "error" => 0,
                    "msg" => 'something went wrong, most likely file is to large for upload. check upload_max_filesize, post_max_size and memory_limit in you php.ini',
        );
            }
      
            //print json_encode($response);
            
        }else{
          $response = array(
                           "error" => '0',
                           "msg" => 'Invalid input',
                   );   
        }
        $this->set('response', $response); 
        $this->render("ajax");
    }
    public function changecoverphoto($userid=null){ 
        configure::write("debug",0);
        $this->layout = 'ajax';
        $this->loadModel('User');
        if ($this->request->is('post')) {
            $imagePath = WWW_ROOT."files".DS."coverphoto".DS; 
            $allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
            $temp = explode(".", $_FILES["img"]["name"]);
            $image = $_FILES["img"]["name"];
            $image = str_replace(' ', '_', $image);
            $current_date =date("Ymdhis"); 
            $extension =pathinfo($image, PATHINFO_EXTENSION);
            $extension_old = end($temp); 
            //Check write Access to Directory 
            if(!is_writable($imagePath)){
                $response = Array( "status" => 'error', "message" => 'Can`t upload File; no write Access' ); 
            }
    
            if ( in_array($extension, $allowedExts)){
                if ($_FILES["img"]["error"] > 0){
                    $response = array( "status" => 'error', "message" => 'ERROR Return Code: '. $_FILES["img"]["error"]);           
                }else{
                    $filename = $_FILES["img"]["tmp_name"];
                    list($width, $height) = getimagesize( $filename );
                    $image_name= $current_date.$image;
                    if(move_uploaded_file($filename,  $imagePath . $image_name)){
                        $this->loadModel('Gallery');
                        $this->request->data['Gallery']['image']=$image_name;
                        $this->request->data['Gallery']['user_id']=$userid;
                        $this->request->data['Gallery']['type']="landscape";
                        $this->Gallery->create();
                        if($this->Gallery->save($this->request->data)){
                            $gallery="Data saved into gallery and update coverphoto"; 
                        }else{
                            $gallery="Data did/'t saved into gallery and coverphoto";
                        }
                        $this->User->id=$userid;
                        $this->User->saveField("coverphoto",$image_name);
                    } 
                    /*$imageUrl="https://" . $_SERVER['SERVER_NAME']. "/files/coverphoto/original/";*/
                    $imageUrl=host. DS."files".DS."coverphoto".DS;
                    $response = array(
                          "status" => 'success',
                          "url" => $imageUrl.$image_name,
                          "width" => $width,
                          "height" => $height,
                          "data_save"=>$gallery
                    );
                }
            }
            else
            {
             $response = array(  "status" => 'error', "message" => 'something went wrong, most likely file is to large for upload. check upload_max_filesize, post_max_size and memory_limit in you php.ini' );
            }
       
        }else{
            $response = array( "status" => 'error',  "message" => 'Invalid Input' );

        }
            $this->set('response', $response);  
            $this->render("ajax");
    }
   
    public function terms($userid = null){
        configure::write("debug",0);
            $this->layout ="ajax";
            $this->loadModel('User');
            $this->loadModel('Staticpage');
            // $userid = $this->Auth->User("id");       
            $ds = $this->User->find("first", array("conditions" => array("User.id" => $userid)));
            if($ds){
                // $this->layout = '_inner';
            } else {
               // $this->layout = 'default'; 
            }
            $data=$this->Staticpage->find('first',array('conditions'=>array('AND'=>array('Staticpage.position'=>'terms','Staticpage.status'=>1)))); 
            if(!empty($data)){
             $data['Staticpage']['description'] = htmlentities($data['Staticpage']['description']);   
            $response['terms_policy'] = $data;
             $response['error'] = 1;
             $response['msg'] = "Terms and policy page";
         }else{
            $response['error'] = 0;
            $response['msg'] = "No Terms and policy content found";
         }
             
            $this->set('response', $response); 
            $this->render("ajax");
        }
public function coutactus($userid=NULL) {
          configure::write("debug",0);
          $this->layout="ajax"; 
          if ($this->request->is('post')) { 
            $response['error'] = 1;
            $response['msg'] = 'Thank You! For contacting Mysponsers.com, one of our representatives will be contacting you as soon as possible.';
        }else{
            $response['error'] = 0;
            $response['msg'] = 'You failed to contact. Please, try again.'; 
        }
        $this->set('response',$response);
        $this->render('ajax');
    }  
public function needsponsers($id = null, $pagi = null){
    configure::write("debug",0);
    $this->layout="ajax"; 
    $this->loadModel('User');
    $conditions_male = array('User.has_sponsers'=>1,'User.sex'=>'Male','User.id <>'=>$id);
    $conditions_female = array('User.has_sponsers'=>1,'User.sex'=>'Female','User.id <>'=>$id);
    $allMaleSponsers = $this->User->find('all',array(
              'contain'=>false,
              'conditions'=>$conditions_male,
              'fields'=>array(
                  'id',
                  'firstname',
                  'email',
                  'image',
                  'since_sponsers'
                ),'limit'=>10,'offset' =>$pagi
           )
         );
        if($allMaleSponsers){
            foreach ($allMaleSponsers as $allMaleSponserss) {
                $fileExtention = pathinfo ( $allMaleSponserss['User']['image'], PATHINFO_EXTENSION ); 
                if($fileExtention): 
                    $filename1 = FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$allMaleSponserss['User']['image']; 
                    $filename = FULL_BASE_URL . $this->webroot ."files".DS."profile".DS."original".DS.$allMaleSponserss['User']['image'];
                 if (@getimagesize($filename1)) {
                $allMaleSponserss['User']['image'] =FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$allMaleSponserss['User']['image'];
                }else if(@getimagesize($filename)){
                    $allMaleSponserss['User']['image'] =FULL_BASE_URL . $this->webroot ."files".DS."profile".DS."original".DS.$allMaleSponserss['User']['image'];
                }else{
                $allMaleSponserss['User']['image'] =FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png"; 
                }
                 else:
                $allMaleSponserss['User']['image'] =FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                endif;  
              $resp[] = $allMaleSponserss;
                
            } 
            $response['malesponsers_list'] = $resp;
            $response['error'] = 1;
            $response['msg'] = "list of user Male sponser";
          }else{
            $response['alreadysponsers_list'] = array();
            $response['error'] = 0;
            $response['msg'] = "No list  found";
          }
      $allFemaleSponsers = $this->User->find('all',array(
              'contain'=>false,
              'conditions'=>$conditions_female,
              'fields'=>array(
                  'id',
                  'firstname',
                  'email',
                  'image',
                  'since_sponsers'
                ),'limit'=>10,'offset' =>$pagi
           )
         );
        if($allFemaleSponsers){
            $resp = array();
            foreach ($allFemaleSponsers as $allFemaleSponserss) {
                $fileExtention = pathinfo ( $allFemaleSponserss['User']['image'], PATHINFO_EXTENSION ); 
                if($fileExtention):
                    $filename1 = FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$allFemaleSponserss['User']['image'];
                    $filename = FULL_BASE_URL . $this->webroot ."files".DS."profile".DS."original".DS.$allFemaleSponserss['User']['image'];
                 if (@getimagesize($filename1)) {
                $allFemaleSponserss['User']['image'] =FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$allFemaleSponserss['User']['image'];
                }else if(@getimagesize($filename)){
                    $allFemaleSponserss['User']['image'] =FULL_BASE_URL . $this->webroot ."files".DS."profile".DS."original".DS.$allFemaleSponserss['User']['image'];
                }else{
                $allFemaleSponserss['User']['image'] =FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png"; 
                }
                else:
                $allFemaleSponserss['User']['image'] =FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                endif;  
              $resp[] = $allFemaleSponserss;
                
            }
            $response['femalesponsers_list'] = $resp;
            $response['error'] = 1;
            $response['msg'] = "list of user Female sponser";
          }else{
            $response['alreadysponsers_list'] = array();
            $response['error'] = 0;
            $response['msg'] = "No list  found";
          }
    
          $isUseralreadySponser = $this->User->find('all',array('contain'=>false,'conditions'=>array('User.id'=>$id),'fields'=>array('id','has_sponsers','image','firstname','lastname','email','since_sponsers')));
         
          if($isUseralreadySponser){ 
            $resp = array();
            foreach ($isUseralreadySponser as $isUseralreadySponsers) {
                $fileExtention = pathinfo ( $isUseralreadySponsers['User']['image'], PATHINFO_EXTENSION ); 
                if($fileExtention):
                    $filename1 = FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$isUseralreadySponsers['User']['image'];
                    $filename = FULL_BASE_URL . $this->webroot ."files".DS."profile".DS."original".DS.$isUseralreadySponsers['User']['image'];
                 if(@getimagesize($filename1)) {
                $isUseralreadySponsers['User']['image'] =FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$isUseralreadySponsers['User']['image'];
                }else if(@getimagesize($filename)){
                    $isUseralreadySponsers['User']['image'] =FULL_BASE_URL . $this->webroot ."files".DS."profile".DS."original".DS.$isUseralreadySponsers['User']['image'];
                }else{
                $isUseralreadySponsers['User']['image'] =FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png"; 
                }
                else:
                $isUseralreadySponsers['User']['image'] =FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
                endif;  
              $resp[] = $isUseralreadySponsers;
                
            } 
            $response['alreadysponsers_list'] = $resp;
            $response['error'] = 1;
            $response['msg'] = "list of user already sponser";
          }else{
            $response['alreadysponsers_list'] = array();
            $response['error'] = 0;
            $response['msg'] = "No list  found";
          }

    $this->set('response',$response);
    $this->render('ajax');
}          
public function changepassword($id = NULL) {
    configure::write("debug",0);
          $this->layout="ajax"; 
          $this->loadModel('User');
        if ($this->request->is('post')) {
            $password = AuthComponent::password($this->data['User']['old_password']);
            // $em = $this->Auth->user('username');
            $pass = $this->User->find('first', array('conditions' => array('AND' => array('User.password' => $password, 'User.id' => $id))));
            if ($pass) {
                if ($this->data['User']['new_password'] != $this->data['User']['cpassword']) {
                    $response['error'] = 0;
                    $response['msg'] = "New password and Confirm password field do not match";
                    // $this->Session->setFlash(__("New password and Confirm password field do not match"));
                } else {
                    $this->User->data['User']['password'] = $this->data['User']['new_password'];
                    $this->User->id = $pass['User']['id'];
                    if ($this->User->exists()) {
                        $pass['User']['password'] = $this->data['User']['new_password'];
                        if ($this->User->save()) {
                            // $this->Session->setFlash(__("Password Updated"));
                            $response['error'] = 1;
                            $response['msg'] = "Password Updated";
                            // $this->redirect(array('controller' => 'users', 'action' => 'changepassword'));
                        }
                    }
                }
            } else {
                // $this->Session->setFlash(__("Your old password did not match."));
                $response['error'] = 0;
                $response['msg'] = "Your old password did not match.";
            }
        }else{
          $response['error'] = 0;
          $response['msg'] = "Invalid Input";  
        }
        $usr = $this->User->find('first', array('conditions' => array('User.id' => $id), 'fields' => array('User.id', 'User.profile_status', 'messageid', 'pciv_status', 'support_ststus','sound_notifications','color'), 'recursive' => 0));
        if($usr){
            $response['list'] = $usr;
        }else{
            $response['list'] = array();
        }
    $this->set('response',$response);
    $this->render('ajax');
    }
public function becomeAsponser(){
  configure::write("debug",0);
  $this->layout="ajax";
  if (!empty($this->request->is('post'))) {  
    $ms = 'Sub:- '.$this->request->data['subject'].'<br/>'.$this->request->data['message']." <br/> ".$this->request->data['email'];
    try{
          $l = new CakeEmail('smtp');
//                            debug($l); exit;
        $l->emailFormat('html')->template('default', 'default')->subject('Become A Sponsers')->to('contact@mysponsers.com')->send($ms);
        $this->set('smtp_errors', "none");  
        $res['email'] = $this->request->data['email'];
        $res['subject'] = $this->request->data['subject'];
        $res['message'] = $this->request->data['message'];
        $response['list'] = $res;
        $response['error'] = 1;
        $response['msg'] = "Sponser request Sent";
    }catch(Exception $e){ 
        $response['error'] = 0;
        $response['msg'] = "Sponser request Failed";
    }
    // contact@mysponsers.com 
  }else{
    $response['error'] = 0;
    $response['msg'] = "Sponser request Failed";
  }  
  $this->set('response',$response);
  $this->render('ajax');  
 }     
 public function viewmyfriend($id = null,$loggedid = null){
    Configure::write('debug', 0);
    $this->layout="ajax";
    if(!empty($id)){ 
    $usr = $this->User->find('first', array('conditions' => array('User.id' => $id),'fields'=>array('User.id','User.firstname','User.email','User.image','User.original_image','User.coverphoto','User.country','User.state','User.city','User.phone','User.cell_no','User.home','User.sex','User.dob','User.born_address','User.raised_child','User.where_live','User.where_lived','User.where_visited','User.family_member','User.relation_ship','User.workplace_company','User.addict_alco_type','User.addtimezone','User.time_addict_alco','User.highschool','User.college','User.company','User.position','User.designation','User.city_town','User.color','User.workplace_description','User.professional_skill','User.college_passout','User.highschool_passout','User.current_city_town','User.yourhometown','User.your_places','User.meeting','User.about_you','User.support_ststus'),'recursive'=>-1));
           if($usr){
           $fileExtention = pathinfo ( $usr['User']['image'], PATHINFO_EXTENSION );
            if($fileExtention):
            $usr['User']['image']= FULL_BASE_URL . $this->webroot ."files".DS."profile".DS.$usr['User']['image'];
            else:
            $usr['User']['image']= FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."default-user-icon-profile.png";
            endif;
            $fileExtention = pathinfo ( $usr['User']['coverphoto'], PATHINFO_EXTENSION );
            if($fileExtention):
            $usr['User']['coverphoto']= FULL_BASE_URL . $this->webroot ."files".DS."coverphoto".DS.$usr['User']['coverphoto'];
            else:
            $usr['User']['coverphoto']= FULL_BASE_URL . $this->webroot ."inner".DS."images".DS."cover_photo1.jpg";
            endif;
            $this->loadModel('Support');
            $dataAbout = $this->Support->find("count", array('conditions' => array('AND'=>array('Support.user_id'=>$loggedid,'Support.touserid'=>$id)))); 
             $usr['User']['friend_support_status'] = $dataAbout;   
             
            /*if(empty($usr['User']['country']))
                $usr['User']['country'] = array();
            if(empty($usr['User']['state']))
                $usr['User']['state'] = array();
            if(empty($usr['User']['city']))
                $usr['User']['city'] = array();
            if(empty($usr['User']['phone']))
                $usr['User']['phone'] = array();
            if(empty($usr['User']['cell_no']))
                $usr['User']['cell_no'] = array();
            if(empty($usr['User']['home']))
                $usr['User']['home'] = array();
            if(empty($usr['User']['sex']))
                $usr['User']['sex'] = array();
            if(empty($usr['User']['dob']))
                $usr['User']['dob'] = array();
            if(empty($usr['User']['born_address']))
                $usr['User']['born_address'] = array();
            if(empty($usr['User']['raised_child']))
                $usr['User']['raised_child'] = array();
            if(empty($usr['User']['where_live']))
                $usr['User']['where_live'] = array();
            if(empty($usr['User']['where_lived']))
                $usr['User']['where_lived'] = array();
            if(empty($usr['User']['where_visited']))
                $usr['User']['where_visited'] = array();
            if(empty($usr['User']['family_member']))
                $usr['User']['family_member'] = array();
            if(empty($usr['User']['relation_ship']))
                $usr['User']['relation_ship'] = array();
            if(empty($usr['User']['workplace_company']))
                $usr['User']['workplace_company'] = array();
            if(empty($usr['User']['addict_alco_type']))
                $usr['User']['addict_alco_type'] = array();
            if(empty($usr['User']['addtimezone']))
                $usr['User']['addtimezone'] = array();
            if(empty($usr['User']['time_addict_alco']))
                $usr['User']['time_addict_alco'] = array();
            if(empty($usr['User']['highschool']))
                $usr['User']['highschool'] = array();
            if(empty($usr['User']['college']))
                $usr['User']['college'] = array();
            if(empty($usr['User']['company']))
                $usr['User']['company'] = array();
            if(empty($usr['User']['position']))
                $usr['User']['position'] = array();
            if(empty($usr['User']['designation']))
                $usr['User']['designation'] = array();
            if(empty($usr['User']['city_town']))
                $usr['User']['city_town'] = array();
            if(empty($usr['User']['color']))
                $usr['User']['color'] = array();
            if(empty($usr['User']['workplace_description']))
                $usr['User']['workplace_description'] = array();
            if(empty($usr['User']['professional_skill']))
                $usr['User']['professional_skill'] = array();
            if(empty($usr['User']['college_passout']))
                $usr['User']['college_passout'] = array();
            if(empty($usr['User']['highschool_passout']))
                $usr['User']['highschool_passout'] = array();
            if(empty($usr['User']['current_city_town']))
                $usr['User']['current_city_town'] = array();
            if(empty($usr['User']['yourhometown']))
                $usr['User']['yourhometown'] = array();
            if(empty($usr['User']['your_places']))
                $usr['User']['your_places'] = array();
            if(empty($usr['User']['meeting']))
                $usr['User']['meeting'] = array();
            if(empty($usr['User']['about_you']))
                $usr['User']['about_you'] = array(); */
        }
       // debug($usr); exit;
        if(!empty($usr)){
            $response['list'] = $usr;
            $response['error'] = 1;
            $response['msg'] = "Friend profile info";
        }else{
            $response['list'] = array();
            $response['error'] = 0;
            $response['msg'] = "Invalid friend profile info";
        }
       
     }else{
            $response['list'] = array();
            $response['error'] = 0;
            $response['msg'] = "Invalid user id";
        }
        $this->set('response',$response);
        $this->render('ajax');
 } 
 public function faq() {
        configure::write("debug",2);
        $this->layout="ajax"; 
        $faq[0]['que'] = "Que - How do I send a message to someone who has a private page?";
        $faq[0]['ans'] = 'Ans - You must first click on the user’s profile that you want to send a message to, and then click on the “Support” tab to the bottom right of their landscape photo. Once you are supporting them, then you will find them on your support group page under the “I’m Supporting“ tab, then the drop down will give you a send message option.';
        $faq[1]['que'] = "Que - How do I start supporting someone?";
        $faq[1]['ans'] = "Ans - You must first click on the user’s profile that you want to send a message to, and then click on the “Support” tab to the bottom right of their landscape photo.";
        $faq[2]['que'] = "Que - Who can see my posts?";
        $faq[2]['ans'] = "Ans - Anyone can see your post that are  on your profile’s home page if your profile is marked as public, but when you post something it automatically goes to all your “Supporters” (people who are supporting you.)";
        $faq[3]['que'] = "Que - Who can I share posts with?";
        $faq[3]['ans'] = 'Ans - You can only share with people who you are “Supporting” you.';
        $faq[4]['que'] = "Que - How accurate is the members on line number?";
        $faq[4]['ans'] = "Ans - This number is extremely accurate, because it is pulled from our database for each member as they are logged in in real time. So, each time your screen refreshes this number is updated.";
        $faq[5]['que'] = "Que - How do I submit a Photo contribution to go onto the sites landing page?";
        $faq[5]['ans'] = "Ans - At the bottom of every page along the footer tab you will see an option called “Photo Contribution” once you select this option all the details will be explain to you there.";
        $faq[6]['que'] = 'Que - Why is the logo spelled with an “e” instead of the correct “o” before the r?';
        $faq[6]['ans'] = 'Ans - The “e” is there instead of the “o” to represent the eye we are keeping on each other.';
        $faq[7]['que'] = "Que - How do I change the color of my background?";
        $faq[7]['ans'] = "Ans - To change the color of your background you must first click the drop down by your name on the black navigation bar, and then select edit profile. You will then see a default drop down directly to the right of the title Basic Information. This will give you the option to choose several colors.";
        $faq[8]['que'] = "Que - How many invitations can I send out?";
        $faq[8]['ans'] = "Ans - You can send out as many invitations as you desire.";
        $faq[9]['que'] = "Que - Why can’t I send .wmv files through a message?";
        $faq[9]['ans'] = "Ans - wmv files are not supported at this point by our messaging system; however you can upload these files by posts."; 
        if(!empty($faq)){
            $response['list'] = $faq;
            $response['error'] = 1;
            $response['msg'] = "Faq list";
        }else{
            $response['error'] = 0;
            $response['msg'] = "No faq list";
        }
        $this->set('response',$response);
        $this->render('ajax');

    }
public function groupsupport_unsupport($userid = null){ 
         configure::write("debug",0);
          $this->layout="ajax"; 
        if(!$this->request->is("post")){

            $response['list'] = array();
            $response['error'] = 0;
            $response['msg'] = "Invalid input Information";

        }else{
        $this->loadModel('Support');    
        $this->request->data['Support']['type'] = 'self';

        $this->request->data['Support']['user_id'] = $userid;

        $cnt = $this->Support->find("count",array("conditions"=>array("Support.user_id"=>$this->request->data['Support']['user_id'],"Support.touserid"=>$this->request->data['Support']['touserid'])));
        
        $this->loadModel('User');
        $me = $this->User->findById($this->request->data['Support']['user_id']);
        $supporting = $this->User->findById($this->request->data['Support']['touserid']);
        // debug($me); exit;
        
        if($cnt == 0){
            $this->Support->create();
            if($this->Support->save($this->request->data)){
                if($supporting['User']['support_ststus'] == 0){
                    $link = "http://".$_SERVER['SERVER_NAME']."/users/userview/".$me['User']['id'];
                    $my_email = $me['User']['email'];
                    $support_mail = $supporting['User']['email'];
                    $my_name = ucwords($me['User']['firstname'])." ".ucwords($me['User']['lastname']);
                    $support_name = ucwords($supporting['User']['firstname'])." ".ucwords($supporting['User']['lastname']);
                    $subject =$my_name." has started supporting you";
                    if($me['User']['sex']=='Female'){
                        $sex = "her";
                        $sex_1="her";
                    }else{
                        $sex="his";
                        $sex_1="him";
                    }
                    $message = ucwords($me['User']['firstname'])." ".ucwords($me['User']['lastname'])."  has started supporting you. To view ".$sex." profile, or to start supporting ".$sex_1.", please click on the following link: " ;
                    $message .='<a href="'.$link.'">'.$my_name.'</a>';
                    try{
                    $Email = new CakeEmail();
                    $Email->template('default');
                    $Email->emailFormat('html');
                    $Email->from(array("no-reply@mysponsers.com" => "My Sponsers"));
                    $Email->to($support_mail);
                    $Email->subject("$subject");
                    $Email->send("$message");
                }catch(Exception $e){

                }
                } 
                $response['support_resp'] = 1;
                $response['unsupport_resp'] = 0;
                $response['error'] = 1;
                $response['msg'] = "Support success";

            }else{
                $response['support_resp'] = 0;
                $response['unsupport_resp'] = 0;
                $response['error'] = 1;
                $response['msg'] = "Support failed";
            } 

        }else{

            $t=$this->Support->deleteAll(array("Support.user_id"=>$this->request->data['Support']['user_id'],"Support.touserid"=>$this->request->data['Support']['touserid']));

        if($t){

                $response['unsupport_resp'] = 1;
                $response['support_resp'] = 0;
                $response['error'] = 1;
                $response['msg'] = "unsupport Success";

            }else{
                $response['unsupport_resp'] = 0;
                $response['support_resp'] = 0;
                $response['error'] = 0;
                $response['msg'] = "unsupport Failed";
            }

        }
      }
        $this->set('response',$response);
        $this->render('ajax');

    }    
    public function share_post_onhomepage($loginid = null,$postid = null,$sharewith = null) {
        configure::write("debug",2);
        $this->layout = "ajax";
        if (!empty($loginid)) {
            $post_id = $postid;
            if ($post_id) {
                $share_msg = @$this->request->data['message'];
                if ($sharewith) {
                    $share_with = $sharewith;
                } else {
                    $share_with = $loginid;
                }
                $this->loadModel('Post');
                $last_record = $this->Post->find("first", array(
                    "conditions" => array(
                        "AND" => array(
                            "Post.user_id" => $loginid,
                            "Post.share_msg" => $share_msg,
                            "Post.share_with" => $share_with
                        )
                    ),
                    'order' => array('Post.id Desc')
                ));

                if (empty($last_record)) {
                    $this->request->data['Post']['status'] = '1';
                    $this->request->data['Post']['user_id'] = $loginid;
                    $row = $this->Post->findById($post_id);
                    unset($row['Post']['id']);
                    $data = $row['Post'];
                    // $this->Post->save($data);
                    if ($this->Post->save($data)) {
                        $date = date("Y-m-d h:i:s");
                        if ($row['Post']['share_with'] != 0 && $row['Post']['ref_id'] == 0) {
                            $ref_id = $row['Post']['user_id'];
                        } else if ($row['Post']['share_with'] != 0 && $row['Post']['ref_id'] != 0) {
                            $ref_id = $row['Post']['ref_id'];
                        } else if ($row['Post']['share_with'] == 0 && $row['Post']['ref_id'] == 0 && $row['Post']['user_id'] != $loginid) {
                            $ref_id = $row['Post']['user_id'];
                        } else {
                            $ref_id = 0;
                        }
                        $this->Post->id = $this->Post->getInsertID();
                        $this->Post->save(array(
                            "created" => $date,
                            "modified" => $date,
                            "share_msg" => $share_msg,
                            "share_with" => $share_with,
                            "user_id" => $loginid,
                            "ref_id" => $ref_id
                        ));
                    } // And save it
                } else {
                    $response['error'] = 1;
                    $response['msg'] = "Shared in home page";
                    // $this->set("res", array('q' => 0));
                }
            } else {
                $response['error'] = 1;
                $response['msg'] = "Shared in home page";
                // $this->set("res", array('q' => 0));
            }
            $response['error'] = 1;
            $response['msg'] = "Shared in home page"; 
            // $this->set("res", array('q' => 1));
            
        }else{
          $response['error'] = 0;  
          $response['msg'] = "Invalid post"; 
        }
        $this->set('response',$response);
        $this->render('ajax');
    }
 public function blockmysupport($userid = null){
            configure::write('debug',0);
            $this->layout="ajax";
            $this->loadModel("User");
            $this->loadModel("Support");
            //debug($this->request->data);
            if($this->request->is("post")){
            $ch=$this->Support->find('first',array('conditions'=>array('AND'=>array('Support.user_id'=>$this->request->data['Support']['touserid'],
                 'Support.touserid'=>$userid)),'recursive'=>-1)); 
            if($this->request->data['Support']['support']=='block'){ 
                  //echo $this->request->data['Support']['id']=$ch['Support']['id']; 
                 if($ch['Support']['block']==0){
                   //  debug("hello");
                     //$this->request->data['Support']['block']=1;
                    // $this->request->data['Support']['status']=1;
                     $this->Support->id = $ch['Support']['id'];
                     $this->Support->saveField('block', 1);
                     $this->Support->saveField('status', 1);
                      //$this->Support->save($this->request->data);
                       $response['touserid']=$this->request->data['Support']['touserid'];
                       $response['msg']="Block Support Success";

                 }else{ 
                    // debug("hello11");
                     //$this->request->data['Support']['block']=0;
                    // $this->request->data['Support']['status']=0;
                     $this->Support->id = $ch['Support']['id'];
                     $this->Support->saveField('block', 0);
                     $this->Support->saveField('status', 0);
                      //$this->Support->save($this->request->data);
                       $response['touserid']=$this->request->data['Support']['touserid'];
                       $response['msg']="unBlock Support Success";
                 }
            } 
          }else{
            $response['error']= 0;
            $response['msg']= "Invalid input";
          }
            $this->set('response',$response);
            $this->render("ajax");
            
        }
        public function delete_supporter($userid=null, $friendid=null){
            configure::write('debug',0);
            $this->layout = 'ajax'; 
            $this->loadModel("Support");
            $ch=$this->Support->find('first',array('conditions'=>array('AND'=>array('Support.user_id'=>$friendid,
                 'Support.touserid'=>$userid)),'recursive'=>-1));
            // debug($ch); exit;
             $this->Support->id = $ch['Support']['id']; 
            if (!$this->Support->exists()) {
                $response['error'] = 0;
                $response["msg"] = "Invalid supporter";

            }else{
            // $this->request->allowMethod('post', 'delete');
            if ($this->Support->delete()) {
                $response['error'] = 1;
                $response["msg"] = "supporter deleted";   
           }else{
                $response['error'] = 0;
                $response["msg"] = "supporter failed to delete";
           } 
          }
            $this->set('response',$response);
            $this->render("ajax");
        }
        public function chat_load($userid = null){
             configure::write('debug',2);
            // $this->loadModel('UserChatDelete');
            $arv = array();
            /*$deleted_chat_user = $this->UserChatDelete->find('all',array('fields'=>
                  
                   array('UserChatDelete.deleted_to'),array('conditions'=>array(
                             'UserChatDelete.deleted_by'=> ( int ) $this->Auth->user('id') 
                    ))));

               $deleted_chats = [];
               if( count( $deleted_chat_user ) > 0 ){
                foreach( $deleted_chat_user as $key=>$value ):
                       array_push($deleted_chats,$value['UserChatDelete']['deleted_to']);
                     endforeach;
               }*/

            $this->layout='ajax';
            //$timezone=$this->request->data['timezone'];
 
            $date = strtotime("-1000 day");
 
            $dt= date('Y-m-d H:i:s', $date);
 
            // $userid=$this->Auth->User('id');
            $this->loadModel('Onlines');
            $i=0;
          /* original query $convers=$this->Message->find('all',array('conditions'=>array(
                'OR'=>array(
                    'Message.user_id'=>$userid,
                    'Message.friend_id'=>$userid
                    ),
                'AND'=>array(
                    'Message.last_msg'=>'1',
                    'Message.created >='=>$dt
                    ),
               "Message.msg_delete_by !="=>$this->Auth->User('id'),
                ),
                
                'order'=>array('Message.created'=>'desc'),'recursive'=>-1));*/
                /* optimized query start by vipin date 9 jun for unnecessary field in query */
                 $convers=$this->Message->find('all',array('fields'=>array('Message.id','Message.user_id','Message.status','Message.friend_id','Message.message','Message.filetoupload','Message.sender_local_date','Message.receiver_local_date'),'conditions'=>array(
                'OR'=>array(
                    'Message.user_id'=>$userid,
                    'Message.friend_id'=>$userid
                    ),
                'AND'=>array(
                    'Message.last_msg'=>'1',
                    'Message.created >='=>$dt
                    ),
               "Message.msg_delete_by !="=>$this->Auth->User('id'),
                ),
                
                'order'=>array('Message.created'=>'desc'),'recursive'=>-1));
                
                /* optimized query end */
                
                
                
                 $ids = array();
                 
        if(isset($convers) && count($convers)>0)
         { 
           foreach ($convers as $key => $value) {
            if(  
                in_array($value['Message']['user_id'], $ids)
                &&
                in_array($value['Message']['friend_id'], $ids)

              )
            {
               unset($convers[$key]);
            }else{
                
                 array_push($ids, $value['Message']['user_id']);
                 array_push($ids, $value['Message']['friend_id']);
            }
                    
          }
      }
 //pr($convers);die;
            foreach($convers as $converss):
                   // $date1=$converss['Message']['created'];
                    //$zone=$converss['Message']['timezone'];
                    $sender_local_date=$converss['Message']['sender_local_date'];
                    $receiver_local_date=$converss['Message']['receiver_local_date'];
            //$date= new DateTime($date1, new DateTimeZone($zone)); 
 
             //$date->setTimezone(new DateTimeZone($timezone));
            //$convers[$i]['Message']['created']= $date->format('F j, Y g:i A');
 //$convers[$i]['Message']['created']=$date1;
 
 //edit by gurbinder reagrding date time format//
 $convers[$i]['Message']['sender_local_date']=date("F j, Y g:i A", strtotime($sender_local_date));
 $convers[$i]['Message']['receiver_local_date']=date("F j, Y g:i A", strtotime($receiver_local_date));
  //END//
 $convers[$i]['Message']['message'] = strip_tags($converss['Message']['message']);


            $uid=$converss['Message']['user_id'];
            $frndid=$this->Auth->user("id");
            if($uid == $frndid):
              $uid=$converss['Message']['friend_id'];  
            endif;
 
            $ctt=$this->Message->query("SELECT count(`id`) as `id` FROM `messages` WHERE `friend_id` =$frndid AND `user_id` =$uid AND `status` = '1'");
                     $convers[$i]['Message']['count'] = $ctt[0][0]['id'];
                     $convers[$i]['Message']['none']=1;
 
                    if($converss['Message']['user_id']!=$userid): 
                  $convers[$i]['User']=$this->User->find('first',array('conditions'=>array('User.id'=>$converss['Message']['user_id']),
                      'fields'=>array('User.id','User.firstname','User.lastname','User.email','User.image'),'recursive'=>-1));
                    
                   /* if(!empty($convers[$i]['User']['User']['image'])):
                     $convers[$i]['User']['User']['image']=$this->webroot.'files'.DS.'profile'.DS.$convers[$i]['User']['User']['image'];
                    else:
                     $convers[$i]['User']['User']['image']=$this->webroot.'inner'.DS.'images'.DS.'default-user-icon-profile.png';   
                    endif;*/
                    
                    $filename = FULL_BASE_URL . $this->webroot .'/files/profile/'.$convers[$i]['User']['User']['image'];
                
                if(file_exists($filename) && ! empty( $convers[$i]['User']['User']['image'] )){
                $convers[$i]['User']['User']['image']= FULL_BASE_URL . $this->webroot .'files'.DS.'profile'.DS.$convers[$i]['User']['User']['image'];
              }else{
                $convers[$i]['User']['User']['image'] = "http://www.buira.net/assets/images/shared/default-profile.png";
              }
                    
                    
                   
                   
                   $convers[$i]['Online']=$this->Online->find('first',array('conditions'=>array('Online.user_id'=>$converss['Message']['user_id']),
                      'fields'=>array('Online.id','Online.user_id'),'recursive'=>-1)); 
                      if(empty($convers[$i]['Online'])):
                          $convers[$i]['Online']['Online']['user_id']=0;
                      endif; 
                      $convers[$i]['usr_co']=0;
                elseif($converss['Message']['friend_id'] != $userid):  
                    /*$convers[$i]['User']=$this->User->find('first',array('conditions'=>array('User.id'=>$converss['Message']['friend_id']),
                      'fields'=>array('User.id','User.firstname','User.lastname','User.email','User.image'),'recursive'=>-1));  */
                      /*if(!empty($convers[$i]['User']['User']['image'])):
                     $convers[$i]['User']['User']['image']=$this->webroot.'files'.DS.'profile'.DS.$convers[$i]['User']['User']['image'];
                    else:
                     $convers[$i]['User']['User']['image']=$this->webroot.'inner'.DS.'images'.DS.'default-user-icon-profile.png';   
                    endif;*/
                     /* $filename = $_SERVER['DOCUMENT_ROOT'].$this->webroot.'app/webroot/files/profile/'.$convers[$i]['User']['User']['image'];
                
                if(file_exists($filename)){
                $convers[$i]['User']['User']['image']=$this->webroot.'files'.DS.'profile'.DS.$convers[$i]['User']['User']['image'];
              }else{
                $convers[$i]['User']['User']['image'] = "http://www.buira.net/assets/images/shared/default-profile.png";
              }*/
               $user_v=$this->User->find('first',array('conditions'=>array('User.id'=>$converss['Message']['friend_id']),
                      'fields'=>array('User.id','User.firstname','User.lastname','User.email','User.image'),'recursive'=>-1));  


                if( isset( $user_v['User'] ) && count( $user_v['User'] ) > 0 )
                {
                    $convers[$i]['User'] = $user_v;
                   
                  $filename = FULL_BASE_URL . $this->webroot .'/files/profile/'.$convers[$i]['User']['User']['image'];
                
                    if(file_exists($filename) && ! empty($convers[$i]['User']['User']['image'])){
                    $convers[$i]['User']['User']['image']=FULL_BASE_URL . $this->webroot .'files'.DS.'profile'.DS.$convers[$i]['User']['User']['image'];
                  }else{
                    $convers[$i]['User']['User']['image'] = "http://www.buira.net/assets/images/shared/default-profile.png";
                  }
              } 
                    
                    
                    
                    
                    $convers[$i]['Online']=$this->Online->find('first',array('conditions'=>array('Online.user_id'=>$converss['Message']['friend_id']),
                        'fields'=>array('Online.id','Online.user_id'),'recursive'=>-1));
                        if(empty($convers[$i]['Online'])):
                              $convers[$i]['Online']['Online']['user_id']=0;
                          endif; 
                    $convers[$i]['Message']['Message']['none']=1;      
                   $convers[$i]['usr_co']=0;       
                endif; 
                $i++;
            endforeach;
 
            $conversation=$convers;
         /*  $mid=array();
            foreach($convers as $convers1):
                $mid[]=$convers1['Message']['user_id'];
                $mid[]=$convers1['Message']['friend_id'];
            endforeach;
            $s="";
            
           commented by gurbindar on date 3 jun 2016 
           if(!empty($mid)):

              
                $arv=array_unique($mid); 
              $arv = array_merge($arv,$deleted_chats);
 
            

            $onli=$this->Online->find("all",array('conditions'=>array('NOT'=>array('Online.user_id'=>$deleted_chats)),
                'contain'=>array('User'=>array('id','firstname','lastname','image','email')),'fields'=>array('Online.id','Online.user_id')));
            $j=0;
 
            foreach($onli as $onlis):
                $onli[$j]['Message']=$this->Message->find('first',array('conditions'=>array('AND'=>array('Message.user_id'=>$onlis['Online']['user_id'],'Message.friend_id'=>$userid))));
                $onli[$j]['Message']['Message']['none']=1;
            if(empty($onli[$j]['Message'])){
                $onli[$j]['Message']['Message']['none']=0;
            }
            $onli[$j]['usr_co']=1;
            $j++;
            endforeach;
 
            $conversation=array_merge($convers,$onli);
            endif;
     commented by gurbindar on date 3 jun 2016
            */
            if(empty($conversation)):


              

            $onli=$this->Online->find('all',array('conditions'=>array('NOT'=>array('Online.user_id'=>$this->Auth->user('id'),'NOT'=>array('Online.user_id'=>$arv))),
            'fields'=>array('Online.id','Online.user_id')));

            $j=0;
            if(count($onli)>0):
            $onli[2]['Online']['id']='11';
            $onli[2]['Online']['user_id']='7';


            foreach($onli as $onlis): 
                $onli[$j]['Online']['Online']['user_id']=$onlis['Online']['user_id'];
                $onli[$j]['Message']=$this->Message->find('first',array('conditions'=>array(
                    'AND'=>array('Message.user_id'=>$this->Auth->user('id'),'Message.friend_id'=>$onlis['Online']['user_id'])
                )));
            if(empty($onli[$j]['Message'])):
              $onli[$j]['Message']=$this->Message->find('first',array('conditions'=>array(
                    'AND'=>array('Message.user_id'=>$onlis['Online']['user_id'],'Message.friend_id'=>$this->Auth->user('id'))
                ))); 
            if(!empty($onli[$j]['Message'])):
              $onli[$j]['Message']['Message']['none']=1;  
            endif;
            
            endif; 
             if(empty($onli[$j]['Message'])){
                $onli[$j]['Message']['Message']['none']=0;
            }
            if($onlis['Online']['user_id']!=$userid): 
                  $onli[$j]['User']=$this->User->find('first',array('conditions'=>array('User.id'=>$onlis['Online']['user_id']),
                      'fields'=>array('User.id','User.firstname','User.lastname','User.email','User.image'),'recursive'=>-1));
                    /*if(!empty($onli[$j]['User']['User']['image'])):
                     $onlis[$j]['User']['User']['image']=$this->webroot.'files'.DS.'profile'.DS.$onli[$j]['User']['User']['image'];
                    else:
                     $onli[$j]['User']['User']['image']=$this->webroot.'inner'.DS.'images'.DS.'default-user-icon-profile.png';   
                    endif;*/
                      $filename = FULL_BASE_URL . $this->webroot .'/files/profile/'.$onli[$j]['User']['User']['image'];
                
                if(file_exists($filename) && ! empty($onli[$j]['User']['User']['image'])){
                $onli[$j]['User']['User']['image']=FULL_BASE_URL . $this->webroot .'files'.DS.'profile'.DS.$onli[$j]['User']['User']['image'];
              }else{
                $onli[$j]['User']['User']['image'] = "http://www.buira.net/assets/images/shared/default-profile.png";
              }
                    
                    
                    
                endif;       
            $onli[$j]['usr_co']=0;
            $j++;
            endforeach;
            endif;
            $conversation=$onli;
            endif;
            if(!empty($conversation)):
             // pr($conversation);die;
            $res['list']=$conversation;
            $res['msg']="User list";
            $res['error']=0;
            else:     
            $res['msg']="No list";
            $res['error']=1;  
            $res['messageStatus'] = 504; 
            endif;

           //pr($res);die;  
            $this->set("response",$res);
            $this->render('ajax');

        }
        public function chatunread_mesagecount($userid=null){
            configure::write('debug',2);
            $this->layout='ajax';
            $timezone=$this->request->data['timezone'];
            $localdate=$this->request->data['localdate'];
//            $exp=explode("-",$timz);
//            $timezone=$exp[0]."/".$exp[1];
//            debug($localdate);
            $unread_message = $this->Message->find("count",array(               
                "conditions"=>array(
                    "AND"=>array(
                        "Message.friend_id"=>$userid,
                        "Message.status"=>1
                    )
                )
            ));
            $response=array();
            if(!empty($unread_message)):
                $unread_message_fst = $this->Message->find("first",array(               
                "conditions"=>array("AND"=>array("Message.friend_id"=>$userid,"Message.status"=>1)),
                    'fields'=>array('Message.id','Message.created','Message.timezone','Message.sound_notification'),'order'=>array('Message.id'=>'DESC')));
//               debug($unread_message_fst);
               $date1=$unread_message_fst['Message']['created'];
               $zone=$unread_message_fst['Message']['timezone'];
//               $timezone="Asia/Kolkata";
//               $localdate="2016-05-11 21:13:57";
             $date= new DateTime($date1, new DateTimeZone($zone)); 
 //            date_default_timezone_set('America/Los_Angeles');
//            $timezone="Asia/Kolkata";
             $date->setTimezone(new DateTimeZone($timezone));
            $datetime1= $date->format('Y-m-d H:i:s');
            $datetime1 = new DateTime($datetime1); 
            
             $date2= new DateTime($localdate, new DateTimeZone($zone));  
             $date2->setTimezone(new DateTimeZone($timezone)); 
            $datetime2= $date2->format('Y-m-d H:i:s');
            $datetime2 = new DateTime($datetime2);
            $interval = $datetime1->diff($datetime2);
            $elapsed = $interval->format('%y years %m months %a days %h hours %i minutes %S seconds');
            $year=$interval->format('%y');
            $month=$interval->format('%m');
            $day=$interval->format('%a');
            $hour=$interval->format('%h');
            $minute=$interval->format('%i');
            $second =""; 
            $second=$interval->format('%S'); 
            if(($year=='0')&&($month=='0')&&($day=='0')&&($hour=='0')&&($minute<='3')){
//                debug($second);
                if($second <= '14'){
//                  debug($second);  
                  $response['sound']=0;
                }
            }else{
                   $response['sound']=1; 
                }   
                if(empty(@$_SESSION['num'])):
                    $_SESSION['num']=0;
                endif;
                if($unread_message_fst['Message']['sound_notification']=='1'):
                    $this->Message->query('update messages set sound_notification="0" where id='.$unread_message_fst['Message']['id']);
                $response['sound']=0;
                else:
                    $response['sound']=1;
                endif;
//                $response['num']=$_SESSION['num'];
//                if($unread_message > $_SESSION['num']):
//                    $response['sound']=0;
//                endif; 
                $_SESSION['num']=$unread_message;
                $response['second']=$second;
                $response['error']=0;
                
                $response['count']=$unread_message;
                $response['msg']='Unread msg found';
            else:
                $response['second']=@$second;
                $response['error']=1;
                $response['count']=0;
                $response['sound']=1; 
                $response['msg']='Unread msg not found';
            endif;
            $this->set('response',$response);
            $this->render('ajax');
        }
public function chat1_typing($uid = null){
            // echo "yes";die;
            configure::write('debug',0);
            $this->layout='ajax';
            $frndid=$this->request->data['friend_id'];
            $datetime=$this->request->data['datetime'];
            $timezone=$this->request->data['timezone'];
            // $uid=$this->Auth->User('id');
            if(!empty($frndid)&&!empty($uid)): 
//                echo "UPDATE `messages` set `typing`='1',`typing_date`='$datetime' where  `user_id` =$uid AND `friend_id` =$frndid ORDER BY id DESC limit 1"; echo "<br/>";
            $this->Message->query("UPDATE `messages` set `typing`='1',`typing_date`='$datetime',`timezone`='$timezone' where  `user_id` =$uid AND `friend_id` =$frndid ORDER BY id DESC  limit 1");
            $res['msg']="Save";
            $res['error']=0;
            else:
            $res['msg']="Save failed";
            $res['error']=1;   
            endif; 
//            exit;
 
            $this->set('response',$res);
            $this->render('ajax');
        }  
 public function chat1_list($id=null,$chat_user=null,$chat_frnd=null,$timezone=null,$localdate=null,$uid=null){

           // Configure::write('debug',2);
            $this->layout = 'ajax';
            $typing = 0;
            $comm_id=explode('-', $id);
            $id=$comm_id[0];
            $comm_id=$comm_id[1];
            // $uid=$this->Auth->User('id'); 
            if(($chat_user == 0)&&($chat_frnd == 0)):
                $msg=$this->Message->query("SELECT * FROM `messages` where ((`user_id`=$id and `friend_id`=$uid and `msg_delete_by` !=$uid) or (`user_id`=$uid and `friend_id`=$id and `msg_delete_by` !=$uid)) "
                        . "and ((`active_chat_user`=0 and `active_chat_friend`=1) or (`active_chat_user`=1 and `active_chat_friend`=0)or "
                        . "(`active_chat_user`=1 and `active_chat_friend`=1) or (`active_chat_user`=0 and `active_chat_friend`=0)) order by id ASC");

            else:    
                 $msg1=$this->Message->query("SELECT * FROM `messages` where ((`user_id`=$id and `friend_id`=$uid and `msg_delete_by` !=$uid) or (`user_id`=$uid and `friend_id`=$id and `msg_delete_by` !=$uid)) "
                        . "and ((`active_chat_user`=0 and `active_chat_friend`=1) or (`active_chat_user`=1 and `active_chat_friend`=0)or "
                        . "(`active_chat_user`=1 and `active_chat_friend`=1)) order by id ASC");

             //pr($msg);die;

            if(!empty($msg1)):
            foreach($msg1 as $msgs):
                if($msgs['messages']['user_id']==$uid):
                    $msg=$this->Message->query("SELECT * FROM `messages` where (`user_id`=$uid and `friend_id`=$id) and "
                        . "((`active_chat_user`=1 and `active_chat_friend`=1) or (`active_chat_user`=1 and `active_chat_friend`=0)) order by id ASC");
                    $this->Message->query("UPDATE `messages` set `active_chat_user`=0  WHERE ("
                    . "(`user_id` =$id AND `friend_id` =$uid)OR (`user_id` =$uid AND `friend_id` =$id))");
                endif;
                if($msgs['messages']['friend_id']==$uid):
                    $msg=$this->Message->query("SELECT * FROM `messages` where (`user_id`=$id and `friend_id`=$uid) and "
                        . "((`active_chat_user`=1 and `active_chat_friend`=1) or (`active_chat_user`=0 and `active_chat_friend`=1)) order by id ASC");
                    $this->Message->query("UPDATE `messages` set `active_chat_friend`=0  WHERE ("
                    . "(`user_id` =$id AND `friend_id` =$uid)OR (`user_id` =$uid AND `friend_id` =$id))");
                endif; 
            endforeach; 
            endif;

            /* email forward start ============*/
             $dtc= date('Y-m-d h:i:s', strtotime('-5 minutes')); 
            $mscheck=$this->Message->find('all',array('conditions'=>array('AND'=>array(
                'Message.user_id'=>$uid,'Message.friend_id'=>$id,'Message.created > '=>$dtc,'Message.email_notification'=>'1')),'recursive'=>-1));
           /* commented by vipin no need to send email 
           foreach($mscheck as $mschecks):

           $id=$mschecks['Message']['id'];
           $this->Message->query("update messages set email_notification='0' where id=$id");
           $frnd=$this->User->find('first',array('conditions'=>array('User.id'=>@$frndid),'fields'=>array('User.firstname','User.lastname','User.email'),'recursive'=>-1));
            $support_mail=@$frnd['User']['email'];
            $subject=$this->Auth->User('firstname')." ".$this->Auth->User('lastname')." Sent you a Message";
            $message= $this->Auth->User('firstname')." ".$this->Auth->User('lastname')." has sent you message. To reply his messgae please clcik on the following link <a href='".$this->webroot.'messages'.DS.'chat1'."'>".$this->Auth->User('firstname')." ".$this->Auth->User('lastname')."</a>."; 
            $Email = new CakeEmail();
            $Email->template('default'); 
            $Email->emailFormat('html');
            $Email->from(array("no-reply@mysponsers.com" => "My Sponsers"));
            $Email->to($support_mail);
            $Email->subject($subject);
           // $Email->send($message);
            
           endforeach;*/
           /*========= Email forward end============*/
            endif; 
            $this->Message->query("UPDATE `messages` set `status`=0  WHERE `user_id` =$comm_id AND `friend_id` =$uid");

            $r=0;
            $this->loadModel('User');
            if(!empty($msg)):
                $tmz=  explode("-",$timezone);
                $timezone=$tmz[0]."/".$tmz[1];

            foreach($msg as $msgs){

                if(!empty($msgs['messages']['filetoupload'])):
                $msg[$r]['messages']['filetoupload']=$this->webroot."images".DS."message_files".DS.$msgs['messages']['filetoupload'];
                $ext=  explode('/', $msgs['messages']['type']);
                $msg[$r]['messages']['type']=$ext[0];
                endif;
                $date1=$msgs['messages']['created'];

                    $zone=$msgs['messages']['timezone'];
                    if(empty($zone)):
                        $zone='America/Los_Angeles'; 
                    endif;
                $date= new DateTime($date1, new DateTimeZone($zone)); 
                $date->setTimezone(new DateTimeZone($timezone));
                $msg[$r]['messages']['created']= $date->format('F j, Y g:i A');

                $msg[$r]['messages']['sender_local_date']=date("F j, Y g:i A", strtotime($msgs['messages']['sender_local_date']));

                 $msg[$r]['messages']['receiver_local_date']=date("F j, Y g:i A", strtotime($msgs['messages']['receiver_local_date']));
                  $msg[$r]['messages']['created']=date("F j, Y g:i A", strtotime($msgs['messages']['created']));
                
                $user=$this->User->find('first',array('conditions'=>array('User.id'=>$msgs['messages']['user_id']),
                    'fields'=>array('User.id','User.firstname','User.lastname','User.image','User.last_login'),'recursive' => -1));
                
                //$user['User']['image']=$this->webroot.'files'.DS.'profile'.DS.$user['User']['image'];
                
                
                $filename = WWW_ROOT.'/files/profile/'.$user['User']['image'];
                
                if(file_exists($filename) && !empty($user['User']['image'])){
                $user['User']['image']=$this->webroot.'files'.DS.'profile'.DS.$user['User']['image'];
              }else{
                $user['User']['image'] = "http://www.buira.net/assets/images/shared/default-profile.png";
              }
                
                $msg[$r]['messages']['count'] = $this->Message->find("count",array(               
                     "conditions"=>array("AND"=>array("Message.friend_id"=>$uid,"Message.status"=>1))));
                $msg[$r]['Message']=$user;
                 
                $r++;

            }

            $timdiff=$this->Message->find('first',array('conditions'=>array('AND'=>array('Message.user_id'=>$id,'Message.friend_id'=>$uid)),
                'fields'=>array('Message.typing','Message.typing_date','Message.id','Message.timezone'),'order'=>array('Message.id'=>'DESC'),'recursive'=>-1));

           if( count($timdiff) > 0)
           {
            $date1=$timdiff['Message']['typing_date'];
            $zone=$timdiff['Message']['timezone'];
            $date= new DateTime($date1, new DateTimeZone($zone)); 

             $date->setTimezone(new DateTimeZone($timezone));
            $datetime1= $date->format('Y-m-d H:i:s');

            $datetime1 = strtotime($datetime1);

            $dte=$localdate; 
            $date2= new DateTime($dte, new DateTimeZone($zone));  
             $date2->setTimezone(new DateTimeZone($timezone));
            $datetime2= $date2->format('Y-m-d H:i:s');

            $datetime2 = strtotime($datetime2);

            $interval  = abs($datetime2 - $datetime1);

            $minutes   = round($interval / 60);
           
           if($minutes < 5){
             $typing=1;
            }else{
                $typing=0;
                $this->Message->query("UPDATE `messages` set `typing`='0' where  `user_id` =$id AND `friend_id` =$uid ORDER BY id DESC  limit 1");
            }
        }
            
             
            
            $response['error']=0;
            $response['list']=$msg;
            $response['typing']=$typing;
            $response['msg']="Message list";

            else:

                $tmz=  explode("-",$timezone);
                $timezone=$tmz[0]."/".$tmz[1];
                $timdiff=$this->Message->find('first',array('conditions'=>array('AND'=>array('Message.user_id'=>$id,'Message.friend_id'=>$uid)),
                'fields'=>array('Message.typing','Message.typing_date','Message.id','Message.timezone'),'order'=>array('Message.id'=>'DESC'),'recursive'=>-1));
 
            if(count($timdiff)>0):

            $date1=$timdiff['Message']['typing_date'];

            $zone=$timdiff['Message']['timezone'];

            $date= new DateTime($date1, new DateTimeZone($zone)); 

             @$date->setTimezone(new DateTimeZone($timezone)); 
            $datetime1= $date->format('Y-m-d H:i:s');

            $datetime1 = strtotime($datetime1);

            $dte=$localdate; 
            $date2= new DateTime($dte, new DateTimeZone($zone));  
             $date2->setTimezone(new DateTimeZone($timezone));
            $datetime2= $date2->format('Y-m-d H:i:s');

            $datetime2 = strtotime($datetime2);

            $interval  = abs($datetime2 - $datetime1);

            $minutes   = round($interval / 60);

            if($minutes < 1){
             $typing=1;
            }else{
                $typing=0;
                $this->Message->query("UPDATE `messages` set `typing`='0' where  `user_id` =$id AND `friend_id` =$uid ORDER BY id DESC  limit 1");
            }
                
           endif;     
            $response['error']=1;
            $response['typing']=$typing;
            $response['msg']="No Message list"; 
            endif;
   
 
//pr($response);die;
            $this->set('response',$response);
            $this->render('ajax'); 
        }  
public function chat1_delete($id=null,$loginid=null,$local=null){

          
         $msgInfo = $this->Message->find('first',array('conditions'=>array('Message.id'=>$id),'fields'=>array('Message.friend_id','Message.user_id')));

         $realFriend =  $msgInfo['Message']['friend_id'] == $loginid ? $msgInfo['Message']['user_id']:$msgInfo['Message']['friend_id']; 
         
         if( $this->alreadyDeleted($id=null,$loginid=null) ){
                  
                 $islastMessageUpdate = $this->whichIsLastMessage($realFriend);


          }else{
                 
                $msg_delted_by = $this->updateMsgDeleteBy($id=null,$loginid=null);



                if( $msg_delted_by ){
                     
               $which_messgae_is_last_for_logged_user = $this->whichIsLastMessage($realFriend);
                


                }  


          }
            if( isset($local) && $local ===1 ){
                return true;
            }
            $response['msgid']=$id;
            $response['error']=0;
            $this->set('response',$response);
            $this->render('ajax');
           
      }  
      public function alreadyDeleted($id=null,$loginid=null){
         
          $message = $this->Message->find('first',array('conditions'=>array('Message.id'=>$id),'fields'=>array('Message.msg_delete_by','Message.user_id')));
          

          if( $message['Message']['user_id'] !== $loginid )
          {
           if( isset( $message ) && $message['Message']['msg_delete_by'] > 0){
                    
                     $this->Message->id=$id;
                     if( $this->Message->delete() ){
                        return true;
                     } return false;

           }
       }
                     return false;


      }
    public function whichIsLastMessage($friend_id){

        /*--Consider logged user as sender and friend as Reciever ..here....*/

               $lastId = 0;
               $lastSenderId = $this->senderLastId($friend_id);
               $lastReceiverId = $this->receiverLastId($friend_id);
               $lastId = $lastSenderId > $lastReceiverId ? $lastSenderId : $lastReceiverId;
             
             if( $lastId > 0 )
             {
               $this->Message->id = $lastId;
               if ( $this->Message->saveField('last_msg', 1) ){
                        return true;

                  }return false;
              }
             return true;


      }
   public function updateMsgDeleteBy($id=null,$loginid=null){

            
  /*--Means message was not deleted by either end ...This function update the update MsgDeleteBy by logged user---*/
                
                  $this->Message->id = $id;
                  $loggedUser = $loginid;
                  
                  if ( $this->Message->saveField('msg_delete_by', $loggedUser) ){
                        
                      return true;

                  }
                 
                  return false;;



                 
      } 
  public function msg_post($loginid=null){
            configure::write('debug',0);
            $this->layout="ajax";
//            debug($this->request->data['frnd_id']);
//            debug($this->request->data('msg'));
            if($this->request->is('post')): 
               $this->Message->create();

              $this->loadModel('User');

              
              $me = $this->User->findById($loginid);
              $supporting = $this->User->findById($this->request->data['frnd_id']);

           $sendersTimeZone = $me['User']['tz'];
           $receiversTimeZone = $supporting['User']['tz'];
           
           date_default_timezone_set($sendersTimeZone);
           $this->request->data['Message']['sender_local_date']= date('y-m-d H:i:s');

           date_default_timezone_set($receiversTimeZone);
           $this->request->data['Message']['receiver_local_date']= date('y-m-d H:i:s');




            $this->request->data['Message']['msg_create']=$this->request->data['msg_create'];
            $this->request->data['Message']['user_id']=$loginid;
            $this->Message->data['Message']['friend_id']=$this->request->data['frnd_id'];
            $this->Message->data['Message']['message']=str_replace("&nbsp;", '', $this->request->data['msg']);
           
            $this->Message->data['Message']['timezone']=$this->request->data['timezone'];
            $this->Message->data['Message']['created']=$this->request->data['created'];  
            $usr=$loginid;
            $frndis=$this->request->data['frnd_id'];
            $this->Message->query("UPDATE `messages` set `last_msg`='0' WHERE ((`user_id`=$usr and `friend_id`=$frndis)or(`friend_id`=$usr and `user_id`=$frndis)) and `last_msg`='1'");
//            "SELECT * FROM `messages` WHERE ((`user_id`=7 and `friend_id`=81)or(`friend_id`=7 and `user_id`=81)) and `last_msg`='1'"
//            $this->Message->query();
$this->Message->data['Message']['sound_notification']='1';
$target_file=$this->Message->data['Message']['message'];
            if($this->Message->save($this->request->data)):
                $id=$this->Message->getLastInsertId();
                $response=$this->Message->find('first',array('conditions'=>array('Message.id'=>$id),
                    'fields'=>array('Message.msg_create','Message.sender_local_date,Message.message'),'recursive'=>-1));
                $response['Message']['created']=date("F j, Y g:i A", strtotime($response['Message']['sender_local_date']));
                
                     $dtc= date('Y-m-d'); 
            $mscheck=$this->Message->find('first',array('conditions'=>array('AND'=>array(
                'Message.user_id'=>$this->Auth->User('id'),'Message.friend_id'=>$this->request->data['frnd_id'],'Message.created LIKE'=>$dtc.'%')),'recursive'=>-1));
            if(!$mscheck && !$supporting['User']['messageid']):
              $this->first_emailnotification($this->request->data['frnd_id']);  
            endif;
                 
//                $response['resp']=$luser;
                $response['msg']='Msg Save';
                $response['error']=0;
            else:    
                $response['msg']='Msg not Save';
                $response['error']=1;
            endif;
            endif;
            $this->set('response',$response);
            $this->render('ajax');
        }  

    public function chat1_photo($usr = null){
            configure::write('debug',0);
            $this->layout="ajax"; 

            $this->request->data['Message']['user_id']=$usr;
            $this->request->data['Message']['friend_id']=$this->request->data['friend_id'];
            $this->Message->data['Message']['timezone']=$this->request->data['timezone'];
            $this->Message->data['Message']['message']=$this->request->data['msg'];
            $this->Message->data['Message']['created']=$this->request->data['created'];
            // $usr=$this->Auth->User('id');
            $frndis=$_POST['friend_id'];
            $this->Message->query("UPDATE `messages` set `last_msg`='0' WHERE ((`user_id`=$usr and `friend_id`=$frndis)or(`friend_id`=$usr and `user_id`=$frndis)) and `last_msg`='1'");
            $image=$this->request->data['filetoupload'];
            $ext=$this->request->data['extension'];

             $path = WWW_ROOT . "images".DS."message_files".DS;
             $fileTmpLoc = $_FILES['filetoupload']['tmp_name'];
             $fileType = $_FILES['filetoupload']['type'];
             $filenm=date('ymdhis').".".$ext;
             $target_file = $path .$filenm; 
           $moveResult = move_uploaded_file($fileTmpLoc, $target_file); 
           $this->request->data['Message']['filetoupload']=$filenm;
           $this->request->data['Message']['type']=$fileType;
           
           $this->loadModel('User'); 
          
           $me = $this->User->findById($usr);
           $supporting = $this->User->findById($frndis);

           $sendersTimeZone = $me['User']['tz'];
           $receiversTimeZone = $supporting['User']['tz'];
           
           date_default_timezone_set($sendersTimeZone);
           $this->request->data['Message']['sender_local_date']= date('y-m-d H:i:s');

           date_default_timezone_set($receiversTimeZone);
           $this->request->data['Message']['receiver_local_date']= date('y-m-d H:i:s');

        $this->Message->data['Message']['sound_notification']='1';
   

            if($moveResult == true):
               $this->Message->save($this->request->data);
                $id=$this->Message->getLastInsertId();
                

                $response=$this->Message->find('first',array('conditions'=>array('Message.id'=>$id),'fields'=>array('Message.msg_create','Message.sender_local_date'),'recursive'=>-1));

                 $dtc= date('Y-m-d'); 
            $mscheck=$this->Message->find('first',array('conditions'=>array('AND'=>array(
                'Message.user_id'=>$usr,'Message.friend_id'=>$this->request->data['frnd_id'])),"order"=>array("Message.id Desc"),'recursive'=>-1));
            if(!$mscheck):
              $this->first_emailnotification($this->request->data['frnd_id']);  
            endif;
                $response['msg']='Msg Save';
                $response['error']=0;
            else:    
                $response['msg']='Msg not Save';
                $response['error']=1;
            endif;
            
           
            $this->set('response',$response);
            $this->render("ajax");



        }
    public function pushFcmNotification($target = null , $data = null){
        configure::write('debug',0);
   //FCM API end-point
   $url = 'https://fcm.googleapis.com/fcm/send';
   //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
   $server_key = 'AAAAXRxemkk:APA91bF0GtmmiBfLCpACI-Ajd51mwM1yW4BvwxEPrjuEq3TQxwd48Ge_ZKglY9jl51sO0yD3fwL5uyPwedlQwjvLHNRXvXdOZg70OIxZc1gSh1h9pMtTm8D-pD0svYJqbQSqoN5w0iiP';
  /* $target = "eLBI0-O-aoI:APA91bGvXEXiv2VpqN_f3RoCPYlwhWbX0Gvstm539i9HTcPpW1UIU9Z9Rp5WKbY2cJAs0gREG-ifmIrAJvFCLLSWh3FUCTj0Q0_JPiAEyrghQewsfcABEzdNW8HI8IZUicKmDEwMHPyu";         
    
   $data = array
        (
            'message'   => 'here is a message. message',
            'title'     => 'This is a title. title',
            'subtitle'  => 'This is a subtitle. subtitle',
            'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
            'vibrate'   => 1,
            'sound'     => 1,
            'largeIcon' => 'large_icon',
            'smallIcon' => 'small_icon'
        );*/
        $fields = array();
           $fields['data'] = $data;
           if(is_array($target)){
            $fields['registration_ids'] = $target;
           }else{
            $fields['to'] = $target;
            $fields['priority'] = 'high';
           }
           //header with content_type api key
           $headers = array(
            'Content-Type:application/json',
                'Authorization:key='.$server_key
           );
 
   //CURL request to route notification to FCM connection server (provided by Google)           
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
   $result = curl_exec($ch);
   if ($result === FALSE) {
    die('Oops! FCM Send Error: ' . curl_error($ch));
   }
   curl_close($ch);
   return $result;

    }
    public function iosFCMMessage(){ 
   configure::write('debug',0);
    $this->layout="ajax"; 
    $this->loadModel('User');
    if($this->request->data['userId']){
         $message = "";
        $users = $this->User->find('first',array('conditions'=>array('User.id'=>$this->request->data['userId']),
            'fields'=>array('User.id','User.firstname','User.push_notification'), 'recursive'=>-1));
        
        if($users){
            $this->User->id = $users['User']['id']; 
            if($users['User']['push_notification'] == 1){
                $this->User->query("UPDATE `users` SET `push_notification` ='0' where `id` = ".$users['User']['id']);
                // $this->User->saveField('push_notification', "0");
                $data = array
                    (
                        'message'   => 'You Active your Notification',
                        'title'     => 'Notification Active',
                        'subtitle'  => 'Notification Active',
                        'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
                        'vibrate'   => 1,
                        'sound'     => 1,
                        'largeIcon' => 'large_icon',
                        'smallIcon' => 'small_icon',
                        "badge" => 1,
                        "sound" => "cheering.caf",
                        "alert" => "New data is available"
                    );
                $message = "You Active your Notification";    
            }else{
                $this->User->saveField('push_notification', "1");
                $data = array
                    (
                        'message'   => 'You Deactive your Notification',
                        'title'     => 'Notification Deactive',
                        'subtitle'  => 'This is a subtitle. subtitle',
                        'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
                        'vibrate'   => 1,
                        'sound'     => 1,
                        'largeIcon' => 'large_icon',
                        'smallIcon' => 'small_icon',
                        "badge" => 1,
                        "sound" => "cheering.caf",
                        "alert" => "New data is available"
                    );
                    $message = "You Deactive your Notification";
            }
        }
     
           //FCM API end-point
           $url = 'https://fcm.googleapis.com/fcm/send';
           //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
           $server_key = 'AAAAXRxemkk:APA91bF0GtmmiBfLCpACI-Ajd51mwM1yW4BvwxEPrjuEq3TQxwd48Ge_ZKglY9jl51sO0yD3fwL5uyPwedlQwjvLHNRXvXdOZg70OIxZc1gSh1h9pMtTm8D-pD0svYJqbQSqoN5w0iiP';
            $target = $this->request->data['device_token'];
           // debug($this->request->data['userid']);
           // debug($target); exit;
           /*$target = "eLBI0-O-aoI:APA91bGvXEXiv2VpqN_f3RoCPYlwhWbX0Gvstm539i9HTcPpW1UIU9Z9Rp5WKbY2cJAs0gREG-ifmIrAJvFCLLSWh3FUCTj0Q0_JPiAEyrghQewsfcABEzdNW8HI8IZUicKmDEwMHPyu"; */        
            
           
                $fields = array();
                /*$fields['to'] = $target;
                $fields['priority'] = "high";
                $fields['notification'] = array(
                    "title" => "Your Title", "text" => "Your Text"
                    );
                $fields['data'] = array(
                        "customId" => "122",
                         "badge" => 1,
                         "sound" => "cheering.caf",
                        "alert" => "New data is available"
                    );*/
                   $fields['to'] = $target;
                   $fields['data'] = $data;
                   if(is_array($target)){
                    $fields['registration_ids'] = $target;
                   }else{
                    $fields['to'] = $target;
                    $fields['priority'] = 'high';
                    $fields['priority'] = "high";
                    $fields['notification'] = array(
                        "title" => "Your Title", "text" => "Your Text"
                        );
                   }
                   //header with content_type api key
                   $headers = array(
                    'Content-Type:application/json',
                        'Authorization:key='.$server_key
                   );
         
           //CURL request to route notification to FCM connection server (provided by Google)           
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
           $result = curl_exec($ch);
           if ($result === FALSE) {
            die('Oops! FCM Send Error: ' . curl_error($ch));
           }
           curl_close($ch);
           $results['msg'] = $message;
           $results['devide_response'] = $result;

    }else{
      $results['msg'] = "Invalid User info";
      $results['error'] = 0;  
    }
    
   /*debug($result);
   exit;*/
   $this->set('response',$results);
    $this->render("ajax"); 
  }
  public function androidFCMMessage(){
    configure::write('debug',0);
    $this->layout="ajax"; 
    $this->loadModel('User');
    if($this->request->data['userId']){
         $message = "";
        $users = $this->User->find('first',array('conditions'=>array('User.id'=>$this->request->data['userId']),
            'fields'=>array('User.id','User.firstname','User.push_notification'), 'recursive'=>-1));
        
        if($users){
            $this->User->id = $users['User']['id']; 
            if($users['User']['push_notification'] == 1){
                $this->User->query("UPDATE `users` SET `push_notification` ='0' where `id` = ".$users['User']['id']);
                // $this->User->saveField('push_notification', "0");
                $data = array
                    (
                        'message'   => 'You Active your Notification',
                        'title'     => 'Notification Active',
                        'subtitle'  => 'Notification Active',
                        'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
                        'vibrate'   => 1,
                        'sound'     => 1,
                        'largeIcon' => 'large_icon',
                        'smallIcon' => 'small_icon'
                    );
                $message = "You Active your Notification";    
            }else{
                $this->User->saveField('push_notification', "1");
                $data = array
                    (
                        'message'   => 'You Deactive your Notification',
                        'title'     => 'Notification Deactive',
                        'subtitle'  => 'This is a subtitle. subtitle',
                        'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
                        'vibrate'   => 1,
                        'sound'     => 1,
                        'largeIcon' => 'large_icon',
                        'smallIcon' => 'small_icon'
                    );
                    $message = "You Deactive your Notification";
            }
        }
     
           //FCM API end-point
           $url = 'https://fcm.googleapis.com/fcm/send';
           //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
           $server_key = 'AIzaSyAOvwNLbQ7jWnN4QIM1XljKrg6A0vhgMSE';
           $senderid = "912318482895";
           $target = $this->request->data['device_token'];
           // debug($this->request->data['userid']);
           // debug($target); exit;
           /*$target = "e0wI56lh4aU:APA91bH3zRlH4-GzDapBcO_zw_zUbUTo0RHt1bv9wwdei6WJi-pITGSA1hLNhgpydtA8CppHO4DY6MtFYMcnRvFWQ6ytveL08TAWc0oy0NHPAkwpPyG0H62u0DrUwyY8FeJL9VDUqQq4"; */        
            
           
                $fields = array();
                   $fields['data'] = $data;
                   if(is_array($target)){
                    $fields['registration_ids'] = $target;
                   }else{
                    $fields['to'] = $target;
                    $fields['priority'] = 'high';
                   }
                   //header with content_type api key
                   $headers = array(
                    'Content-Type:application/json',
                        'Authorization:key='.$server_key
                   );
         
           //CURL request to route notification to FCM connection server (provided by Google)           
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
           $result = curl_exec($ch);
           if ($result === FALSE) {
            die('Oops! FCM Send Error: ' . curl_error($ch));
           }
           curl_close($ch);
           $results['msg'] = $message;
           $results['devide_response'] = $result;

    }else{
      $results['msg'] = "Invalid User info";
      $results['error'] = 0;  
    }
    
   /*debug($result);
   exit;*/
   $this->set('response',$results);
    $this->render("ajax");
  }    
    public function sendFCMMessage(){
        configure::write('debug',2);
   //FCM API end-point
   $url = 'https://fcm.googleapis.com/fcm/send';
   //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
   $server_key = 'AAAAXRxemkk:APA91bF0GtmmiBfLCpACI-Ajd51mwM1yW4BvwxEPrjuEq3TQxwd48Ge_ZKglY9jl51sO0yD3fwL5uyPwedlQwjvLHNRXvXdOZg70OIxZc1gSh1h9pMtTm8D-pD0svYJqbQSqoN5w0iiP';
   $target = "eLBI0-O-aoI:APA91bGvXEXiv2VpqN_f3RoCPYlwhWbX0Gvstm539i9HTcPpW1UIU9Z9Rp5WKbY2cJAs0gREG-ifmIrAJvFCLLSWh3FUCTj0Q0_JPiAEyrghQewsfcABEzdNW8HI8IZUicKmDEwMHPyu";         
    
   $data = array
        (
            'message'   => 'here is a message. message',
            'title'     => 'This is a title. title',
            'subtitle'  => 'This is a subtitle. subtitle',
            'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
            'vibrate'   => 1,
            'sound'     => 1,
            'largeIcon' => 'large_icon',
            'smallIcon' => 'small_icon'
        );
        $fields = array();
           $fields['data'] = $data;
           if(is_array($target)){
            $fields['registration_ids'] = $target;
           }else{
            $fields['to'] = $target;
            $fields['priority'] = 'high';
           }
           //header with content_type api key
           $headers = array(
            'Content-Type:application/json',
                'Authorization:key='.$server_key
           );
 
   //CURL request to route notification to FCM connection server (provided by Google)           
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
   $result = curl_exec($ch);
   if ($result === FALSE) {
    die('Oops! FCM Send Error: ' . curl_error($ch));
   }
   curl_close($ch);
   debug($result);
   exit;
  }
}