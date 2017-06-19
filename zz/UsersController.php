<?php
App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');
App::uses('CakeEmail', 'Network/Email');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow(array('admin_login','remoteLogin','getForm', 'register', 'forgetpassword', 'chkemail', 'reset', 'frontlogin', 'profilestatus', 'messagestatus', 'poststatus', 'supportstatus',
            'step1', 'step2', 'step3', 'step4', 'hidepost', 'editpost', 'editcomment', 'autosearch', 'autosearchtop', 'chkeml', 'deletesign', 'ajaxCall','status_active',
            'profile_signup_img_save_to_file','profile_signup_img_crop_to_file','notfound','signup_img_save_to_file','signup_img_crop_to_file','email_varification','confirm',
            'autologin','thankyou','goodBuy'));
    }

    /** 
     * Components
     *
     * @var array
     */
    public $components = array('Auth', 'Paginator', 'Session', 'Date', 'Datamail','Timezone','Track');
        var $helpers = array('Html','Time','Session');
    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->Paginator->paginate());
    }
    /* public function guestLogin() {
         configure::write('debug',0);
        $this->layout = 'ajax'; 
        if ($this->request->is('post')) {

         $this->request->data['User']['email'] = 'info@mysponsers.com';
         $this->request->data['User']['password'] = 'guest4321'; 
 
            $role=$this->User->find('first',array('conditions'=>array('User.email'=>$this->request->data['User']['email']),'fields'=>array('User.role','User.status','User.signup_complete'),'recursive'=>0));
 
            
            if(!empty($role)){


             

          $timezone = $_COOKIE['usertimezone'] != '' ? $_COOKIE['usertimezone'] : 'America/New_York';
            unset($_COOKIE['usertimezone']);
            $this->User->id = $role['User']['id'];
            $this->User->saveField("tz",$timezone);

       

              if ($this->Auth->login()) {  
                $response="Logged in Successfully";
              
                $this->loadModel('Usertrack');
                $geo=$this->Track->loc();
                
                if(empty($geo["geoplugin_city"])):
                    $cu=$this->Usertrack->find('first',array('conditions'=>array('AND'=>array(
                        'OR'=>array('Usertrack.country'=>$geo["geoplugin_countryName"]),
                        'NOT'=>array('Usertrack.city'=>"",'Usertrack.state'=>""),
                        'OR'=>array('Usertrack.country'=>$geo["geoplugin_countryName"],'Usertrack.state'=>$geo["geoplugin_regionName"]))
                        ),'fields'=>array('Usertrack.state','Usertrack.city'),'recursive'=>0));
                $this->request->data['Usertrack']['city']=$cu['Usertrack']['city'];  
                else:  
                $this->request->data['Usertrack']['city'] = $geo["geoplugin_city"]; 
                endif;
                if(empty($geo["geoplugin_regionName"])):
                $this->request->data['Usertrack']['state'] = $cu['Usertrack']['state'];
                else:  
                $this->request->data['Usertrack']['state'] = $geo["geoplugin_regionName"];
                endif; 
                $this->request->data['Usertrack']['country'] = $geo["geoplugin_countryName"];
                $this->request->data['Usertrack']['user_id']=$this->Auth->User('id');
                $this->request->data['Usertrack']['latitude']=$geo["geoplugin_latitude"];
                $this->request->data['Usertrack']['longitude']=$geo["geoplugin_longitude"];
                $this->request->data['Usertrack']['session_id']=session_id(); 
                $this->request->data['Usertrack']['ip']=$geo["ip"];
                $this->request->data['Usertrack']['created']=date("Y-m-d H:i:s");
				$this->request->data['Usertrack']['modified']= date("Y-m-d H:i:s");
                $user=$this->Usertrack->find('first',array('conditions'=>array(
                    'AND'=>array('Usertrack.user_id'=>$this->Auth->User('id'),'Usertrack.ip'=>$geo["ip"])),
                    'fields'=>'Usertrack.id','recursive'=>0)); 
                if(!empty($user['Usertrack']['id'])):  
                    $this->request->data['Usertrack']['id']=$user['Usertrack']['id'];
					$this->request->data['Usertrack']['status']=0;
                    $this->Usertrack->save($this->request->data); 
                else: 
                    $this->Usertrack->save($this->request->data); 
                endif;
                  
                $this->redirect("/users/dashboard");
            } else {
                $response="Not Logged in";
                if(@$role['User']['status']==0):
                    if(@$role['User']['signup_complete']==0):
                       $this->Session->write("err_msg", "Please complete the signup process first");  
                       return $this->redirect("/");
                     else: 
                       $this->Session->write("err_msg", "You have been blocked from this site for unwanted behavior, someone from our team will be contacting you soon.");  
                       return $this->redirect("/");
                    endif;   
                else:
                      $this->Session->write("err_msg", "Login Failed: Invalid Login Info.");
                return $this->redirect("/");
 
                endif;
 
                $this->Session->write("err_msg", "Login Failed: Invalid Login Info.");
                return $this->redirect("/");
            }  
            return $this->redirect("/");
 
            }else{
                $this->Session->write("err_msg", "Login Failed: Invalid Login Info.");
                return $this->redirect("/");
            }
        }else{
            return $this->redirect("/");
        }
        $this->set('response', $response);
        $this->render('ajax');
            
    }*/

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }

        $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
        $this->set('user', $this->User->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }

        if ($this->request->is(array('post', 'put'))) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
            $this->request->data = $this->User->find('first', $options);
        }
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }

        $this->request->allowMethod('post', 'delete');
        if ($this->User->delete()) {
            $this->Session->setFlash(__('The user has been deleted.'));
        } else {
            $this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(array('action' => 'index'));
    }

    public function deleteaccount($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }

        $this->request->allowMethod('post', 'delete');
        if ($this->User->delete()) {
            $this->Session->setFlash(__('The user has been deleted.'));
            return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
        } else {
            $this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
            return $this->redirect(array('controller' => 'users', 'action' => 'edit_profile/' . $id));
        }

//        return $this->redirect(array('action' => 'index'));
    }

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index() {
        $this->layout = "admin";
        $data = $this->User->find('count');
        $this->set('count_user', $data);
        $this->User->recursive = 0;
        if ($this->request->is("post")) {
            if ($this->request->data["keyword"]) {
                $keyword = trim($this->request->data["keyword"]);
                $this->paginate = array("limit" => 3, "conditions" => array("OR" => array(
                            "User.username LIKE" => "%" . $keyword . "%"
                )));
                $this->set("keyword", $keyword);
            }
        }else{
               $this->paginate = array('order'=>'User.id desc'); 
            }
        $this->set('users', $x = $this->Paginator->paginate());
//        debug($x);exit;
    }
     public function admin_useradmin(){
        $this->layout = "admin";
        $data = $this->User->find('count',array("conditions"=>array('OR'=>array('User.role'=>'Admin'))));
        $this->set('count_user', $data);
        $this->User->recursive = 0;
        $this->paginate =array("limit" => 10,'order'=>'User.id desc',"conditions"=>array('OR'=>array('User.role'=>'Admin')));
        $this->set('users', $x = $this->Paginator->paginate()); 
    }
        public function admin_usertype($type=NULL) {
        $this->layout = "admin";
        $data = $this->User->find('count',array("conditions"=>array('OR'=>array('User.sex'=>$type))));
        $this->set('count_user', $data);
        if($type=="Male")
            $title="Male Users";
        else
            $title="Female Users";
        $this->set('title',$title);
        $this->User->recursive = 0; 
                 $this->paginate =array("limit" => 10,'order'=>'User.id desc',"conditions"=>array('OR'=>array('User.sex'=>$type)));
         $this->set('users', $x = $this->Paginator->paginate());
     }
      public function admin_userchoice($type=NULL) {
        $this->layout = "admin";
        $data = $this->User->find('count',array("conditions"=>array('OR'=>array('User.addict_alco_type'=>$type))));
        $this->set('count_user', $data);
        if($type=="Alcoholic")
            $title="Alcoholic Users";
        else
            $title="Addict Users";
        $this->set('title',$title);
        $this->User->recursive = 0; 
 //                debug($keyword);exit;
                $this->paginate =array("limit" => 10,'order'=>'User.id desc',"conditions"=>array('OR'=>array('User.addict_alco_type'=>$type)));
         $this->set('users', $x = $this->Paginator->paginate());
     } 

    public function admin_location($cnty=NULL,$stat=NULL,$city=null){
        configure::write('debug',0);
       $this->layout = "admin"; 
       $country=$this->User->find('all',array('group'=>'User.country','conditions'=>array('NOT'=>array('User.country'=>""))
            ,'fields'=>'User.country','recursive'=>0));  
       $this->User->recursive = 0;  
       if(($cnty !=NULL) && ($stat==NULL)){  
          $state=$this->User->find('all',array('group'=>'User.state','conditions'=>array('User.country'=>$cnty)
              ,'fields'=>array('User.country','User.state'),'recursive'=>0));
          $cities=$this->User->find('all',array('group'=>'User.city','conditions'=>array('AND'=>array('User.country'=>$cnty))
              ,'fields'=>array('User.country','User.state','User.city'),'recursive'=>0));
          $data = $this->User->find('count',array("conditions"=>array('OR'=>array('User.country'=>$cnty)))); 
           $this->paginate =array("limit" => 10,"conditions"=>array('OR'=>array('User.country'=>$cnty)));  
         }if(($cnty != NULL) && ($stat != NULL) && ($city == NULL)){ 
          $state=$this->User->find('all',array('group'=>'User.state','conditions'=>array('User.country'=>$cnty)
              ,'fields'=>array('User.country','User.state'),'recursive'=>0));   
          $cities=$this->User->find('all',array('group'=>'User.city','conditions'=>array('AND'=>array('User.country'=>$cnty,'User.state'=>$stat))
              ,'fields'=>array('User.country','User.state','User.city'),'recursive'=>0));
          $data = $this->User->find('count',array("conditions"=>array('AND'=>array('User.country'=>$cnty,'User.state'=>$stat)))); 
           $this->paginate =array("limit" => 10,"conditions"=>array('AND'=>array('User.country'=>$cnty,'User.state'=>$stat)));
         }if(($cnty != NULL) && ($stat != NULL) && ($city != NULL)){
            $state=$this->User->find('all',array('group'=>'User.state','conditions'=>array('User.country'=>$cnty)
              ,'fields'=>array('User.country','User.state'),'recursive'=>0));   
           $cities=$this->User->find('all',array('group'=>'User.city','conditions'=>array('AND'=>array('User.country'=>$cnty,'User.state'=>$stat))
              ,'fields'=>array('User.country','User.state','User.city'),'recursive'=>0));
           $data = $this->User->find('count',array("conditions"=>array('AND'=>array('User.country'=>$cnty,'User.state'=>$stat,'User.city'=>$city)))); 
           $this->paginate =array("limit" => 10,"conditions"=>array('AND'=>array('User.country'=>$cnty,'User.state'=>$stat,'User.city'=>$city)));
       } 
       foreach($country as $countrys):
           $cnry[]=  trim($countrys['User']['country']);
       endforeach;
                $country=array();
                $country=array_unique($cnry);
      foreach ($state as $state1):
          if($state1['User']['state']!=""): 
           $sta[]=  trim($state1['User']['state']);
           endif;
      endforeach;       
      $state=array();
      $state=array_unique($sta);
//      debug($state);
//      foreach ($cities as $cities1):
//          $cit[]=  trim($cities1['User']['city']);
//      endforeach;       
//      $cities=array();
//      $cities=array_unique($cit);
       $this->set(compact('country','state','cities'));  
       $this->set('count_user', $data);
       $this->set('users', $x = $this->Paginator->paginate());
    } 
    public function admin_userdelete() {
        $this->layout = "admin";
        $this->loadModel("Deleteuser");
        $data = $this->Deleteuser->find('count');
        $this->set('count_user', $data);
        $this->Deleteuser->recursive = 0;
        if ($this->request->is("post")) {
            if ($this->request->data["keyword"]) {
                $keyword = trim($this->request->data["keyword"]);
                $this->paginate = array("limit" => 3,'order'=>'Deleteuser.id desc', "conditions" => array("OR" => array(
                            "Deleteuser.username LIKE" => "%" . $keyword . "%"
                )));
                $this->set("keyword", $keyword);
            }
        }
        $this->set('users', $x = $this->Paginator->paginate());
//        debug($x);exit;
    }
    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        configure::write('debug',0);
        $this->layout = "admin";
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }

        $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
        $this->set('user', $this->User->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add() {
        $this->layout = "admin";
        if ($this->request->is('post')) {
            if ($this->User->hasAny(array('User.username' => $this->request->data['User']['username']))) {
                $this->Session->setFlash(__('Username already exist!!!'));
                return $this->redirect(array('action' => 'admin_add'));
            } else {
                if ($this->User->hasAny(array('User.email' => $this->request->data['User']['email']))) {
                    $this->Session->setFlash(__('Email already exist!!!'));
                    return $this->redirect(array('action' => 'admin_add'));
                } else {
                    $this->User->create();
                    if ($this->User->save($this->request->data)) {
                        $this->Session->setFlash(__('The user has been saved.'));
                        return $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
                    }
                }
            }
        }
    }

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
        $this->layout = "admin";
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The User has been saved'));
                $this->redirect(array('action' => 'admin_index'));
            } else {
                $this->Session->setFlash(__('The User could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->User->read(null, $id);
        }

        $this->set('admin_edit', $this->User->find('first', array('conditions' => array('User.id' => $id))));
    }

    /**
     * admin_delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_delete($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->loadModel('Deleteuser');
                
        $this->User->recursive=0;
        $use=$this->User->find('first',array('conditions'=>array('User.id'=>$this->User->id)));
        if($use){
         $this->request->data['Deleteuser']['id']= $use['User']['id'];   $this->request->data['Deleteuser']['password']= $use['User']['password'];
         $this->request->data['Deleteuser']['role']= $use['User']['role']; $this->request->data['Deleteuser']['firstname']= $use['User']['firstname'];
         $this->request->data['Deleteuser']['lastname']= $use['User']['lastname']; $this->request->data['Deleteuser']['username']= $use['User']['username'];
         $this->request->data['Deleteuser']['email']= $use['User']['email'];  $this->request->data['Deleteuser']['image']= $use['User']['image'];
         $this->request->data['Deleteuser']['coverphoto']= $use['User']['coverphoto']; $this->request->data['Deleteuser']['country']= $use['User']['country'];
         $this->request->data['Deleteuser']['state']= $use['User']['state']; $this->request->data['Deleteuser']['city']= $use['User']['city'];
         $this->request->data['Deleteuser']['phone']= $use['User']['phone']; $this->request->data['Deleteuser']['cell_no']= $use['User']['cell_no'];
         $this->request->data['Deleteuser']['home']= $use['User']['home']; $this->request->data['Deleteuser']['sex']= $use['User']['sex'];
         $this->request->data['Deleteuser']['dob']= $use['User']['dob']; $this->request->data['Deleteuser']['born_address']= $use['User']['born_address'];
         $this->request->data['Deleteuser']['raised_child']= $use['User']['raised_child'];
         $this->request->data['Deleteuser']['where_live']= $use['User']['where_live']; $this->request->data['Deleteuser']['workplace_company']= $use['User']['workplace_company'];
         $this->request->data['Deleteuser']['where_lived']= $use['User']['where_lived']; $this->request->data['Deleteuser']['addict_alco_type']= $use['User']['addict_alco_type'];
         $this->request->data['Deleteuser']['where_visited']= $use['User']['where_visited'];  $this->request->data['Deleteuser']['addtimezone']= $use['User']['addtimezone'];
         $this->request->data['Deleteuser']['family_member']= $use['User']['family_member']; $this->request->data['Deleteuser']['time_addict_alco']= $use['User']['time_addict_alco'];
         $this->request->data['Deleteuser']['relation_ship']= $use['User']['relation_ship']; $this->request->data['Deleteuser']['highschool']= $use['User']['highschool'];
         $this->request->data['Deleteuser']['college']= $use['User']['college'];  $this->request->data['Deleteuser']['company']= $use['User']['company'];
         $this->request->data['Deleteuser']['position']= $use['User']['position'];  $this->request->data['Deleteuser']['city_town']= $use['User']['city_town'];
         $this->request->data['Deleteuser']['designation']= $use['User']['designation'];  $this->request->data['Deleteuser']['color']= $use['User']['color'];
         $this->request->data['Deleteuser']['workplace_description']= $use['User']['workplace_description']; $this->request->data['Deleteuser']['photo_status']= $use['User']['photo_status'];
         $this->request->data['Deleteuser']['college_passout']= $use['User']['college_passout']; $this->request->data['Deleteuser']['professional_skill']= $use['User']['professional_skill'];
         $this->request->data['Deleteuser']['current_city_town']= $use['User']['current_city_town'];  $this->request->data['Deleteuser']['highschool_passout']= $use['User']['highschool_passout'];
         $this->request->data['Deleteuser']['your_places']= $use['User']['your_places']; $this->request->data['Deleteuser']['yourhometown']= $use['User']['yourhometown'];
         $this->request->data['Deleteuser']['about_you']= $use['User']['about_you']; $this->request->data['Deleteuser']['meeting']= $use['User']['meeting'];
         $this->request->data['Deleteuser']['favorite_quotes']= $use['User']['favorite_quotes'];  $this->request->data['Deleteuser']['motto']= $use['User']['motto'];
         $this->request->data['Deleteuser']['status']= $use['User']['status'];  $this->request->data['Deleteuser']['profile_status']= $use['User']['profile_status'];
         $this->request->data['Deleteuser']['messageid']= $use['User']['messageid']; $this->request->data['Deleteuser']['pciv_status']= $use['User']['pciv_status'];
         $this->request->data['Deleteuser']['support_ststus']= $use['User']['support_ststus']; $this->request->data['Deleteuser']['photo_status']= $use['User']['photo_status'];
         $this->request->data['Deleteuser']['photo_status_photo']= $use['User']['photo_status_photo'];  $this->request->data['Deleteuser']['sound_notifications']= $use['User']['sound_notifications'];
         $this->request->data['Deleteuser']['tokenhash']= $use['User']['tokenhash'];  $this->request->data['Deleteuser']['last_login']= $use['User']['last_login'];
         $this->request->data['Deleteuser']['tz']= $use['User']['tz']; $this->request->data['Deleteuser']['email_varification']= $use['User']['email_varification'];
         if(!empty(@$use['User']['signup_complete'])){
         $this->request->data['Deleteuser']['signup_complete']= $use['User']['signup_complete'];
         }else{ $this->request->data['Deleteuser']['signup_complete']='1'; } $this->request->data['Deleteuser']['delete_reason']= '2';
                
         $this->Deleteuser->create();
         $this->Deleteuser->save($this->request->data);
        }
        
        $this->request->allowMethod('post', 'delete');
        if ($this->User->delete()) {
            $this->Session->setFlash(__('The user has been deleted.'));
        } else {
            $this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function admin_deleteall($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        foreach ($this->request['data']['User'] as $k) {
            $this->User->id = (int) $k;
            if ($this->User->exists()) {
                $this->User->delete();
            }
        }

        $this->Session->setFlash(__('Admin deleted....'));
        $this->redirect(array('action' => 'index'));
    }

    public function admin_profiles() {
        $this->layout = "admin";
        $profile = $this->User->find('first', array('conditions' => array(
                'User.id' => $this->Auth->user('id')
            ))
        );
        $this->set('admin_profiles', $profile);
    }

    public function admin_profilesedit($id = NULL) {
        $this->layout = "admin";
        $this->User->id = $id;
        $x = $this->User->find('first', array('conditions' => array('User.id' => $id)));
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The Admin Profile has been saved'));
                $this->redirect(array('action' => 'admin_profiles'));
            } else {
                $this->Session->setFlash(__('The Admin Profile could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
            $this->request->data = $this->User->find('first', $options);
            $this->set("user_edit", $this->request->data);
        }
    }

    public function admin_changepassword() {
        $this->layout = "admin";
        if ($this->request->is('post')) {
            $password = AuthComponent::password($this->data['User']['old_password']);
            $em = $this->Auth->user('username');
            $pass = $this->User->find('first', array('conditions' => array('AND' => array('User.password' => $password, 'User.username' => $em))));
            if ($pass) {
                if ($this->data['User']['new_password'] != $this->data['User']['cpassword']) {
                    $this->Session->setFlash(__("New password and Confirm password field do not match"));
                } else {
                    $this->User->data['User']['password'] = $this->data['User']['new_password'];
                    $this->User->id = $pass['User']['id'];
                    if ($this->User->exists()) {
                        $pass['User']['password'] = $this->data['User']['new_password'];
                        if ($this->User->save()) {
                            $this->Session->setFlash(__("Password Updated"));
                            $this->redirect(array('controller' => 'Users', 'action' => 'admin_profiles'));
                        }
                    }
                }
            } else {
                $this->Session->setFlash(__("Your old password did not match."));
            }
        }
    }

    public function admin_login() {
        if ($this->request->is('post')) {
             $role=$this->User->find('first',array('conditions'=>array('User.email'=>$this->request->data['User']['email']),'fields'=>'User.role','recursive'=>0));
            if(($role['User']['role']=='Admin')|| ($role['User']['role']=='Superadmin')){ 
             if ($this->Auth->login()) {
                $this->redirect("/admin/users");
                $this->Session->setFlash(__('Successfully LoggedIn!!!'));
            } else {
                $this->Session->setFlash(__('Invalid Username or Password, Please Try Again!!!'));
                $this->redirect("/admin/users/login");
            }
            }else{
                $this->Session->setFlash(__('Sorry You are not Administrator!!!'));
                $this->redirect("/admin/users/login"); 
            }
        }
    }

    public function admin_logout() {
        if ($this->Auth->logout()) {
            $this->redirect("/admin/users/login");
        }
    }

    public function admin_activate($id = null) {
        $this->User->id = $id;
        if ($this->User->exists()) {
            $x = $this->User->save(array(
                'User' => array(
                    'status' => '1'
                )
            ));

            $this->Session->setFlash(__("Activated successfully."));
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash(__("Unable to activate."));
            $this->redirect($this->referer());
        }
    }
    public function admin_block($id = null) {
        $this->User->id = $id;
        if ($this->User->exists()) {
            $x = $this->User->save(array(
                'User' => array(
                    'status' => '0'
                )
            ));

            $this->Session->setFlash(__("Activated successfully."));
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash(__("Unable to activate."));
            $this->redirect($this->referer());
        }
    }
    public function admin_deactivate($id = null) {
        $this->User->id = $id;
        if ($this->User->exists()) {
            $x = $this->User->save(array(
                'User' => array(
                    'status' => '0'
                )
            ));

            $this->Session->setFlash(__("Activated successfully."));
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash(__("Unable to activate."));
            $this->redirect($this->referer());
        }
    }

    public function admin_activateall($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        foreach ($this->request['data']['User'] as $k => $v) {
            if ($k == $v) {
                $this->User->id = $v;
                if ($this->User->exists()) {
                    $x = $this->User->save(array(
                        'User' => array(
                            'status' => 1
                        )
                    ));

                    $this->Session->setFlash(__('Selected Users Activated.', true));
                } else {
                    $this->Session->setFlash(__("Unable to Activate Users."));
                }
            }
        }
        $this->redirect($this->referer());
    }

    /**
     * 
     * @param type $id
     * @throws MethodNotAllowedException
     */
    public function admin_inactivateall($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        foreach ($this->request['data']['User'] as $k => $v) {
            if ($k == $v) {
                $this->User->id = $v;
                if ($this->User->exists()) {
                    $x = $this->User->save(array(
                        'User' => array(
                            'status' => 0
                        )
                    ));
                    $this->Session->setFlash(__('Selected Users Deactivated.', true));
                } else {
                    $this->Session->setFlash(__("Unable to Deactivate Users."));
                }
            }
        }
        $this->redirect($this->referer());
    }

    public function register() {
        //debug($this->request->data);exit;
        if ($this->request->is('post')) {
            if ($this->User->hasAny(array('User.email' => $this->request->data['User']['email']))) {
                $this->Session->setFlash(__('Email already exist!!!'));
                return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
            } else {
                $this->User->create();
                $this->request->data['User']['status'] = "1";
                $this->request->data['User']['lastname'] = "";
                $this->request->data['User']['email_varification'] = 0;
                $this->request->data['User']['role'] = 'User';
                $this->request->data['User']['color'] = '#e7e7e7';
                $this->request->data['User']['last_login'] = '0';
                $this->request->data['User']['addtimezone'] = $this->request->data['User']['yr'] . '-' . $this->request->data['User']['mont'] . '-' . $this->request->data['User']['day'];
                if ($this->User->save($this->request->data)) {
                    $this->Session->setFlash(__('Registered Successfully.. Check your inbox for your login details...'));
                    $ids = $this->User->getLastInsertId();
//                    debug($ids); exit;
                    
                    $uses=$this->User->find('first',array('conditions'=>  array('User.id'=>$ids),'fields'=>array('User.id','User.firstname','User.email'),'recursive'=>-1)); 
                        $support_mail=$uses['User']['email']; 
                        $subject=$uses['User']['firstname']." Welcome to Mysponsers.com";
                        $ids=$uses['User']['id'];
                        $key = Security::hash("232", 'sha512', true);
                        $hash = sha1($uses['User']['firstname'] . rand(0, 100));
                        $url = Router::url(array('controller' => 'users', 'action' => 'confirm'), true) . '/'.$ids.'/'. $key . '#' . $hash;
                        $message = "<p> Welcome! ".$uses['User']['firstname']." and thank you for joining Mysponsers, if you would like to log into your account please, <strong><a href=" . $url . ">Click this mysponsers.com link</a></strong>.</p>";
                        $fu['User']['tokenhash'] = $key;
                        $this->User->id = $uses['User']['id'];
                        if ($this->User->saveField('tokenhash', $fu['User']['tokenhash'])) {
                        $Email = new CakeEmail('smtp'); 
                        $Email->template('default'); 
                        $Email->emailFormat('html'); 
                        $Email->from(array("no-reply@mysponsers.com" => "Mysponsers")); 
                        $Email->to($support_mail); 
                        $Email->subject($subject); 
                        $rr= $Email->send($message);
                        $this->set('smtp_errors', "none");
                        }
                    return $this->redirect(array('controller'=>'users','action'=>'autologin'.'/'.$ids)); 
                } else {
                    $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
                    return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
                }
            }
        }
    }






    /*---  Start by Vipin chauhan----Make remote user authontication---------------*/
    /*
       * Here we implement basically usres login via mysponsers.com
         * 3 way handshaking will goes here.
 Process:=>

    *1.user request login via mysponsers
        *2.fill up email and password.
            *3.data return to the requested server after successfully loggin.with tokenhash.
                *4.requested server save user and return the redirect url.
                    *5.same tokenhash value will append to that request and user will automatically redirect to that user's page.

    */




    public function getForm(){
        Configure::write('debug',0);
        $this->layout = '_inner';
        $returnUrl = $_GET['returnUrl'] !== '' ? $_GET['returnUrl'] : '';
        $response = array();
        $out = '';
       if( isset( $returnUrl ) && !empty( $returnUrl ) && !filter_var($returnUrl, FILTER_VALIDATE_URL) === false )
       { 
        if( $this->request->is( 'get' ) ) {
            $this->Session->write('returnUrl', $returnUrl);   
            $response = $this->render('/Users/remote_login');
            $out .= $response->body();
            echo $out;die;
           }else{
            $response['Error'] =true;
            $response['errorCode'] = 403;
            $response['errorMessage'] = "Invalid request.";
            echo json_encode($response);die;
            } 
        }else{
              
            $response['Error'] =true;
            $response['errorCode'] = 403;
            $response['errorMessage'] = "Return url not recognized.";
            echo json_encode($response);die;

        }  
                   
    }
    public function remoteLogin(){
            App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
            Configure::write('debug',0); 
            $returnUrl = $this->Session->read('returnUrl'); 
            $response = array();
            $this->layout="";
            if( $this->request->is( 'post' ) ) {
                $data = $this->request->data;

                /*$email = trim( $data['User']['email'] );
                $password = $data['User']['password'];*/
                $email = trim( $data['email'] );
                $password = $data['password'];
                $passwordHasher = new SimplePasswordHasher(array('hashType' => 'SHA1'));
                $password = $passwordHasher->hash($password);
                $isUSer = $this->User->find('first',array('conditions'=>array(
                     'email'=>$email,
                     'password'=>$password,
                     'status' => 1
                    ),
                'contain'=>false)
                );

                if ( ! empty( $isUSer ) && count( $isUSer['User'] ) > 0) {


                 foreach ($isUSer as $key => $value) {

                    $filename = $_SERVER['DOCUMENT_ROOT'].$this->webroot.'app/webroot/files/profile/'.$value['image'];

                      if ($value['image'] && file_exists ( $filename )) {    
                        $isUSer[$key]['image'] = FULL_BASE_URL . $this->webroot . 'files/profile/' . $value['image'];
                      }else{
                        $isUSer[$key]['image'] = "http://www.buira.net/assets/images/shared/default-profile.png";
                     }
                     $isUSer[$key]['coverphoto'] = FULL_BASE_URL . $this->webroot . 'files/coverphoto/' . $value['coverphoto'];
                 }
                if( empty( $isUSer['User']['tokenhash'] ) ) {
                    $isUSer['User']['tokenhash'] = sha1($isUSer['User']['email'] . rand(0,100));
                }

 


                 unset($isUSer['User']['password']);
                 $userInformation =$isUSer;


                 if( count( $userInformation ) > 0 && !empty( $userInformation ) ) {    
                    $response['Error'] =false;
                    $response['SuccessCode'] = 200;
                    //$response['returnUrl'] = trim($returnUrl);
                    $response['successMessage'] = "User logged with mysponsers.com";
                    $response['userInformation'] = $userInformation;
                   /* echo json_encode($response);
                    header ("Location: $returnUrl");*/
                    /*----Send curl data ----------------------*/
                    $curl = curl_init($returnUrl);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($response));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $response11 = curl_exec($curl);
                    $json = json_decode($response11, true);
                    curl_close($curl);
                    $this->Session->delete('returnUrl');
                     if( $json['status'] === 'false' )
                    {
                    $response1['Error'] =false;
                    $response1['SuccessCode'] = 200;
                    $response1['redirecturl'] = $json['Page'];
                    $response1['tokenhash'] = ''; 
                    $response1['message'] = $json['message'];
                    }else{
                    $response1['Error'] =false;
                    $response1['SuccessCode'] = 200;
                    $response1['redirecturl'] = $json['Page'];
                    $response1['tokenhash'] =$userInformation['User']['tokenhash'];
                    $response1['message'] = $json['message'];
                    } 
                    echo json_encode($response1);die;
                    /*----Send end curl data--------------------*/

                    } 
                   }else{
                    $response['Error'] =true;
                    $response['errorCode'] = 403;
                    $response['errorMessage'] = "Invalid login details.";
                    }   
                    echo json_encode($response);die;
            } 
           
           
    }
    /*---  End by Vipin----Make remote user authontication---------------*/







    
    public function confirm($id = null) {
         configure::write('debug',0);
 //        $this->layout = "ajax";
        $i = base64_decode($id);
//        debug($i);exit;
        $this->User->id = $id;
        $check = $this->User->find('first', array('conditions' => array('User.id' => $id), 'fields' => array('User.status','User.email_varification')));
        if ($check['User']['status'] == '1' && $check['User']['email_varification'] == 1) {
            $this->Session->setFlash(__('Your account has been already  Verified!'));
            return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
        } else {
            $this->request->data['User']['status'] = 1;
            $this->request->data['User']['email_varification'] = 1;
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('Your account has been  Verified!'));
                return $this->redirect(array('controller' => 'users', 'action' => 'email_varification'."/".$id));
            }
        }
        return $this->redirect(array('controller' => 'users', 'action' => 'notfound'));
    }
    public function notfound(){
        
    }
    public function email_varification($id=NULL){  
        if(!empty($id)): 
            $this->set('id',$id);
        else:    
            return $this->redirect(array('controller' => 'users', 'action' => 'notfound'));
        endif;
    }

    public function frontlogin() {

        configure::write('debug',2);
        $this->layout = 'ajax';
        if ($this->request->is('post')) {
//            debug($this->request->data);exit;
            $role=$this->User->find('first',array('conditions'=>array('User.email'=>$this->request->data['User']['email']),'fields'=>array('User.role','User.status','User.signup_complete'),'recursive'=>0));
//            if($role['User']['role']=='User'){
            if(!empty($role)){


               /*--Start by vipin for update the users timeZone-----*/   

          $timezone = $_COOKIE['usertimezone'] != '' ? $_COOKIE['usertimezone'] : 'America/New_York';
            unset($_COOKIE['usertimezone']);
            $this->User->id = $role['User']['id'];
            $this->User->saveField("tz",$timezone);

           
                

       /*--Start by vipin for update the users timeZone-----*/

            if ($this->Auth->login()) {
                $response="Logged in Successfully";
                
            $this->loadModel('Usertrack');
                $geo=$this->Track->loc();
                if(empty($geo["geoplugin_city"])):
                    $cu=$this->Usertrack->find('first',array('conditions'=>array('AND'=>array(
                        'OR'=>array('Usertrack.country'=>$geo["geoplugin_countryName"]),
                        'NOT'=>array('Usertrack.city'=>"",'Usertrack.state'=>""),
                        'OR'=>array('Usertrack.country'=>$geo["geoplugin_countryName"],'Usertrack.state'=>$geo["geoplugin_regionName"]))
                        ),'fields'=>array('Usertrack.state','Usertrack.city'),'recursive'=>0));
                $this->request->data['Usertrack']['city']=$cu['Usertrack']['city'];  
                else:  
                $this->request->data['Usertrack']['city'] = $geo["geoplugin_city"];
                endif;
                if(empty($geo["geoplugin_regionName"])):
                $this->request->data['Usertrack']['state'] = $cu['Usertrack']['state'];
                else:  
                $this->request->data['Usertrack']['state'] = $geo["geoplugin_regionName"];
                endif; 
                $this->request->data['Usertrack']['country'] = $geo["geoplugin_countryName"];
                $this->request->data['Usertrack']['user_id']=$this->Auth->User('id');
                $this->request->data['Usertrack']['latitude']=$geo["geoplugin_latitude"];
                $this->request->data['Usertrack']['longitude']=$geo["geoplugin_longitude"];
                $this->request->data['Usertrack']['session_id']=session_id(); 
                $this->request->data['Usertrack']['ip']=$geo["ip"];
                $this->request->data['Usertrack']['created']=date("Y-m-d H:i:s");
				$this->request->data['Usertrack']['modified']= date("Y-m-d H:i:s");
                $user=$this->Usertrack->find('first',array('conditions'=>array(
                    'AND'=>array('Usertrack.user_id'=>$this->Auth->User('id'))),
                    'fields'=>'Usertrack.id','recursive'=>0)); 
                if(!empty($user['Usertrack']['id'])):
                    $this->request->data['Usertrack']['id']=$user['Usertrack']['id'];
					$this->request->data['Usertrack']['status']=0;
                    $this->Usertrack->save($this->request->data); 
                else: 
                    $this->Usertrack->save($this->request->data); 
                endif;

                $this->redirect("/users/dashboard");
            } else {
                $response="Not Logged in";
                if(@$role['User']['status']==0):
                    if(@$role['User']['signup_complete']==0):
                       $this->Session->write("err_msg", "Please complete the signup process first");  
                       return $this->redirect("/");
                     else: 
                       $this->Session->write("err_msg", "You have been blocked from this site for unwanted behavior, someone from our team will be contacting you soon.");  
                       return $this->redirect("/");
                    endif;   
                else:
                      $this->Session->write("err_msg", "Login Failed: Invalid Login Info.");
                return $this->redirect("/");
//                return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
                endif;
//                debug('fgdfg');
//                $this->redirect('/');
                $this->Session->write("err_msg", "Login Failed: Invalid Login Info.");
                return $this->redirect("/");
            }
//            debug("df");exit;
            return $this->redirect("/");
//            }else{
//                $this->Session->write("err_msg", "Login Failed: Invalid User Type.");
//                $this->redirect('/');
            }else{
                $this->Session->write("err_msg", "Login Failed: Invalid Login Info.");
                return $this->redirect("/");
            }
        }else{
            return $this->redirect("/");
        }
        $this->set('response', $response);
        $this->render('ajax');
       // exit;f
                
    }

    public function login() {
        if ($this->request->is('post')) {
            
        }
    }
    public function goodBuy(){

         

    }

    public function logout() {
        $this->User->save(array(
            'id' => $this->Session->read('Auth.User.id'),
            'last_login' => '1'
        ));
        $this->loadModel('Online');
         $this->Online->deleteAll(array("Online.user_id" => $this->Auth->user('id')));
        if ($this->Auth->logout()) {
            $this->redirect("/users/goodBuy");
        }
    }

    public function chkemail() {
        Configure::write('debug', 0);
        $this->layout = 'ajax';
        $uname = $this->request->data['User']['email'];
        $response = $this->User->find('first', array('conditions' => array('User.email' => $uname)));
        if ($response) {
            echo "false";
            exit;
        } else {
            echo "true";
            exit;
        }
        $this->set('response', $response);
        $this->render('ajax');
    }

    /**
     * 
     */
    public function chkeml() {
        Configure::write('debug', 0);
        $this->layout = 'ajax';
        $uname = $_POST['data']['User']['email'];
        $response = $this->User->find('count', array('conditions' => array('User.email' => $uname)));
        if ($response > 0) {
            echo 'true';
            exit;
        } else {
            echo 'false';
            exit;
        }
        $this->set('response', $response);
        $this->render('ajax');
    }

    public function forgetpassword() {
        $this->User->recursive = -1;
        if (!empty($this->data)) {
            if (empty($this->data['User']['email'])) {
                $this->Session->setFlash('Please Provide Your Email Address that You used to Register with Us');
            } else {
                $email = $this->data['User']['email'];
                $fu = $this->User->find('first', array('conditions' => array('User.email' => $email)));
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
                            $l = new CakeEmail('smtp');
                            $l->emailFormat('html')->template('default', 'default')->subject('Reset Your Password')->to($fu['User']['email'])->send($ms);
                            $this->set('smtp_errors', "none");
                            $this->Session->setFlash(__('Check Your Email To Reset your password', true));
                            //$this->redirect(array('controller' => 'Pages', 'action' => 'display'));
                        } else {
                            $this->Session->setFlash("Error Generating Reset link");
                        }
                    } else {
                        $this->Session->setFlash('This Account is Blocked. Please Contact to Administrator...');
                    }
                } else {
                    $this->Session->setFlash('Email does Not Exist');
                }
            }
        }
    }

    public function reset($token = null) {
        $this->User->recursive = -1;
        if (!empty($token)) {
            $u = $this->User->findBytokenhash($token);
            if ($u) {
                $this->User->id = $u['User']['id'];
                if (!empty($this->data)) {
                    if ($this->data['User']['password'] != $this->data['User']['password_confirm']) {
                        $this->Session->setFlash("Password doesnot match.");
                        return;
                    }
                    $this->User->data = $this->data;
                    $this->User->data['User']['email'] = $u['User']['email'];
                    $new_hash = sha1($u['User']['email'] . rand(0, 100)); //created token
                    $this->User->data['User']['tokenhash'] = $new_hash;
                    if ($this->User->validates(array('fieldList' => array('password', 'password_confirm')))) {
                        if ($this->User->save($this->User->data)) {
                            $this->Session->setFlash('Password Has been Updated. For login <a href="/" style="color: rgb(255, 255, 255); text-decoration: underline;">Click Here</a>');
//                            $this->redirect(array('controller' => 'Users', 'action' => 'login'));
                        }
                    } else {
                        $this->set('errors', $this->User->invalidFields());
                    }
                }
            } else {
                $this->Session->setFlash('Once you click the password reset link, it will expire. To reset the password, click "Forgot your passward?" again to generate a new link.');
            }
        } else {
            $this->Session->setFlash('Pls try again...');
            $this->redirect(array('controller' => 'pages', 'action' => 'home'));
        }
    }

    public function dashboard() {
        Configure::write('debug', 0);
        $this->layout = '_inner';
        /* track user ================*/
        $this->loadModel('Usertrack');
                $geo=$this->Track->loc();
                if(empty($geo["geoplugin_city"])):
                    $cu=$this->Usertrack->find('first',array('conditions'=>array('AND'=>array(
                        'OR'=>array('Usertrack.country'=>$geo["geoplugin_countryName"]),
                        'NOT'=>array('Usertrack.city'=>"",'Usertrack.state'=>""),
                        'OR'=>array('Usertrack.country'=>$geo["geoplugin_countryName"],'Usertrack.state'=>$geo["geoplugin_regionName"]))
                        ),'fields'=>array('Usertrack.state','Usertrack.city'),'recursive'=>0));
                $this->request->data['Usertrack']['city']=$cu['Usertrack']['city'];  
                else:  
                $this->request->data['Usertrack']['city'] = $geo["geoplugin_city"];
                endif;
                if(empty($geo["geoplugin_regionName"])):
                $this->request->data['Usertrack']['state'] = $cu['Usertrack']['state'];
                else:  
                $this->request->data['Usertrack']['state'] = $geo["geoplugin_regionName"];
                endif;  
                $this->request->data['Usertrack']['country'] = $geo["geoplugin_countryName"];
                $this->request->data['Usertrack']['user_id']=$this->Auth->User('id');
                $this->request->data['Usertrack']['latitude']=$geo["geoplugin_latitude"];
                $this->request->data['Usertrack']['longitude']=$geo["geoplugin_longitude"];
                $this->request->data['Usertrack']['session_id']=session_id(); 
                $this->request->data['Usertrack']['ip']=$geo["ip"];
                $this->request->data['Usertrack']['created']=date("Y-m-d H:i:s");
				$this->request->data['Usertrack']['modified']= date("Y-m-d H:i:s");
                $user=$this->Usertrack->find('first',array('conditions'=>array(
                    'AND'=>array('Usertrack.user_id'=>$this->Auth->User('id'))),
                    'fields'=>'Usertrack.id','recursive'=>0)); 
                if(!empty($user['Usertrack']['id'])):
                    $this->request->data['Usertrack']['id']=$user['Usertrack']['id'];
					$this->request->data['Usertrack']['status']=0;
                    $this->Usertrack->save($this->request->data); 
                else: 
                    $this->Usertrack->save($this->request->data); 
                endif;
                /*=========== END ======*/
        $this->loadModel('Post');
        $this->loadModel('Support');
        $this->loadModel('Like');
         if ($this->request->is('post')) {
            $this->request->data['Post']['status'] = '1';
            $this->request->data['Post']['user_id'] = $this->Auth->user('id');
            $this->Post->create();
            if ($this->Post->save($this->request->data)) {
                $this->Session->setFlash(__('The post has been saved.'));
                return $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
            }
        }

        $this->Session->delete('lastGroupNumber');
        $this->Session->delete('lastGroupNumber_my');
        $this->Session->delete('lastGroupNumber_supported');
        

       /*---------------------For support Users---------------------------------------*/
        $user_id = $this->Auth->user('id');
        $total_groups_supported = 0;
        $total_groups = 0;
        $total_groups_my = 1;
        $supported_user = $this->Support->find('all',array(
                    'fields'=>array(
                          'user_id',
                          'touserid'
                        ),
                   'contain'=>false,'conditions'=>array(
                    
                    'AND'=>array(
                              
                               'Support.status'=>0,
                               'Support.block'=>0
                        ),
                    'OR'=>array(
                                'Support.user_id'=>$user_id,
                                'Support.touserid'=>$user_id,
                               ),

            )));
         $supporting_user = array();



               $only_post_support  =  $this->Like->find('all',array(
                'fields'=>array('post_id'),
                'contain'=>false,'conditions'=>array(
                  'user_id'=>$this->Auth->User('id')
                )));   
             
            if( count( $supported_user ) > 0 || count( $only_post_support  )  > 0 ) {


               $support = [];
               $post_support = []; 
               if(count( $supported_user ) > 0)
               {
                    foreach( $supported_user as $supported )
                    {
                      $supporting_user[] = $supported['Support']['user_id'];
                      $supporting_user[] = $supported['Support']['touserid'];
                     
                   }
                 $support = array_unique($supporting_user);

                 if(($key = array_search($user_id, $support)) !== false) {
                        unset($support[$key]);
                    }
            }


            if( count( $only_post_support ) > 0 ) {
                   
                    foreach ($only_post_support as $key => $value) {
                        $post_support[] = $value['Like']['post_id'];
                    }
            }


                
              
             
            $total_groups_supported = $this->Post->find('count', array(
            'conditions' => array(
                       'OR' => array(
                         'AND' => array(
                            'Post.user_id'=>$support
                            //'Post.status' => '1'
                            ),
                         'Post.id'=>$post_support,
                          
                        )),
            'contain' =>false));
           
          
             
            
        }
        /*---------------------For support User end------------------------------------*/

         /*---------------------For public Users---------------------------------------*/

          $public_profile_user = $this->User->find('all', array('fields'=>array('id'),'contain'=>false,'conditions' => array(
                                 'AND'=>array(
                                 'User.profile_status' => 0,
                                 'User.id <>'=>$this->Auth->User('id')
                                 )
                              )));


       if ( $public_profile_user && count( $public_profile_user ) > 0 ) {
                foreach ($public_profile_user as $public_profile) {
                    $arr1[] = $public_profile['User']['id'];
                }
               $arr1[] = $user_id;

            $total_groups = $this->Post->find('count', array(
            'conditions' => array('AND' => array('Post.user_id'=>$arr1
            	//'Post.status' => '1'
            	)),
            'contain' =>false));






            }


        /*---------------------For public Users---------------------------------------*/


        /*---------------------For Self Posts---------------------------------------*/
      
            $total_groups_my = $this->Post->find('count', array(
            'conditions' => array('AND' => array('Post.user_id' => $user_id
            	//'Post.status' => '1'
            	)),
            'contain' =>false));
           
            if( ($total_groups_my % 5) != 0 ) {
            $remaning = $total_groups_my % 5;

            $total_groups_my = ( int ) ( ( 5 - $remaning ) + ( $total_groups_my+1 ) );
           }else{
            $total_groups_my = $total_groups_my + 1;
           }

             
            
            $this->set(compact('total_groups_supported'));
            $this->set(compact('total_groups'));
            $this->set(compact('total_groups_my'));

         

        /*---------------------For Self POsts---------------------------------------*/

      

    }
     public function autoload_process_my() {  
       
        $post_sid = Array();
        Configure::write('debug', 0);
        $this->layout = "ajax";
        $this->loadModel('Support');
        $this->loadModel('Comment');
        $this->loadModel('Share');
        $this->loadModel('Like');

        $loginUser = $this->Auth->User('id');
        if (isset($this->request->data["current_userid"])) {
            $userId = $this->request->data["current_userid"];
        } else {
            $userId = $this->Auth->User('id');
        }






      $arr1 = array();
      $supporting = false;
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

            // users supports me
            $supportlist1_old = $this->Support->find('all', array('conditions' => array(
                    'AND' => array('Support.touserid' => $userId, 'Support.block' => 0)
                ), 'fields' => array('Support.user_id'), 'recursive' => -1));
            
             // users supports me
            $supportlist1 = $this->Support->find('all', array('conditions' => array(
                    'AND' => array('Support.touserid' => $this->Auth->user('id'), 'Support.block' => 0)
                ), 'fields' => array('Support.user_id'), 'recursive' => -1));
          
        }
       
        //$arr1[] = $userId;

       }else if(isset( $_POST['public'] ) &&  $_POST['public'] === "1"){

       $public_profile_user = $this->User->find('all', array('fields'=>array('id'),'contain'=>false,'conditions' => array(
                                 'AND'=>array(
                                 'User.profile_status' => 0,
                                 'User.id <>'=>$this->Auth->User('id')
                                 )
                              ), 'order' => array('User.id' => 'DESC')));


       if ( $public_profile_user && count( $public_profile_user ) > 0 ) {
                foreach ($public_profile_user as $public_profile) {
                    $arr1[] = $public_profile['User']['id'];
                }
            }
       $arr1[] = $loginUser;
      

       }else{

           $arr1[] = $userId;  
           $this->set('welcome_come',1);
       }  




        if (!$supporting && $userId != $loginUser) { // user and login users are diffrent and not $supporting
            $arr1[] = -1;
        } else { // assign invaid user id
            $arr1[] = -1;
        }
//debug($arr1);
        /* ===========list all friend id============== */
        $allusr = $this->User->find('all', array('conditions' => array('AND' => array('User.id IN' => $arr1)), 'fields' => array('User.id'), 'recursive' => 0));
        foreach ($allusr as $allusrs) {
            $fid[] = $allusrs['User']['id'];
        }
        //array_unique($fid);
        // in query works with array count higher > 1 so assign invaid user id
        if (sizeof($fid) == 1) { // if array count is 1 add one invalid entry
            $fid[] = -1;
        } elseif (sizeof($fid) == 0) { // if array count is 2 add two invalid entry
            $fid[] = -1;
            $fid[] = -1;
        }

        $items_per_group = isset($_POST['record_per_page']) ? $_POST['record_per_page'] : 5;
        //$group_number = filter_var($_POST["group_no"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
        $group_number = (int) $_POST["group_no"];
        
        



            $issameSendJust_my = $this->Session->read('lastGroupNumber_my');
 $increment = 0;
        if( $group_number === $issameSendJust_my ){
        	 $increment = 1;
        }

        $this->Session->write('lastGroupNumber_my', $issameSendJust_my);

   $position = ( ( $group_number + $increment ) * $items_per_group);
        if (empty($position)) {
            setcookie("welcomepost", "0", time() - 3600);
        }


        $this->loadModel('Post');
        $count1 = $this->Post->find('count', array(
            'conditions' => array('AND' => array('Post.user_id IN' => $fid
            	//'Post.status' => '1'
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
       
       // debug($test);
//        $shared = array();
//        foreach($test as $t){
//            array_push($shared, $t['Post']['user_id']);
//        }
        $other=array(0,$this->Auth->user('id'));
//        $shared = array_merge($shared,$other);
//        $shared = array_unique($shared);
//        debug($shared);
      //  debug($fid);
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
                            'Post.share_with' => $this->Auth->user('id'),
                            //'Post.share_with ==' => 'Post.user_id'
                        ),
                        'Post.share_with' => $other
                       // 'Post.status' => '1'
                    )
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
            'order' => array('Post.modified DESC'),
            'limit' => $items_per_group,
            'offset' => $position,
            'recursive' => 1,
        ));
//      debug($result);

        $nextPageAvailable = $count1 > ($items_per_group + $position) ? true : false;
        
        $resu = array();
        foreach ($result as $results) {          
            // debug($results['Share']);
            if ($results['User']['image']) {
                $results['User']['image'] = FULL_BASE_URL . $this->webroot . 'files/profile/' . $results['User']['image'];
            }
            $results['Post']['created'] = $this->Date->time_elapsed_string($results['Post']['created']);

            //$time = strtotime("$created");
            //$results['Post']['created']=$this->Timezone->Timezone_ctime($time);
            
            if ($results['Post']['ref_id'] != '0') {
                $tu = $this->User->find('first', array('conditions' => array('User.id' => $results['Post']['ref_id']), 'fields' => array('firstname', 'lastname'), 'recursive' => 0));
//                debug($tu);
                $results['Post']['refername'] = ucfirst($tu['User']['firstname']) . " " . ucfirst($tu['User']['lastname']);
            }
            if ($results['Like']) {
                $results['Post']['likecount'] = sizeof($results['Like']);
            } else {
                $results['Post']['likecount'] = 0;
            }
            if ($results['Comment']) {
                $results['Post']['commentcount'] = sizeof($results['Comment']);
            } else {
                $results['Post']['commentcount'] = 0;
            }
            
             $this->loadModel('PostView');
            $all_data=$this->PostView->find('count',array('conditions' => array('PostView.post_id' => $results['Post']['id'])));
              
             
                $results['Post']['view_count'] =$all_data;
            
            
           $comment_count = count($results['Comment']);
           for($i=0; $i<$comment_count; $i++){
               $created = $results['Comment'][$i]['created'];
                $time = strtotime($created);
                // $results['Comment'][$i]['created'] = $this->Timezone->Timezone_ctime($time);
                 $results['Comment'][$i]['created'] = $this->getTimeDiffrence($time);
           }

            //debug($cmnt);
            foreach ($results['Share'] as $share) {
                $post_sid[] .= $share['post_id'];
                $shared_post = $this->Post->find('first', array('conditions' => array('Post.id' => $post_sid), 'recursive' => 1));
                $shares[] = $shared_post;
            }
            $resu[] = $results;
        }
//        debug($resu);
        $res = array();
        if ($supporting || $userId == $loginUser) {
            $count2 = $this->Share->find('count', array('conditions' => array('OR' => array('Share.user_id' => $userId, 'Share.share_with' => $userId))));
            $qery = $this->Share->find('all', array('conditions' => array('OR' => array('Share.user_id' => $userId, 'Share.share_with' => $userId)), 'limit' => $items_per_group, 'offset' => $position,));
            if (!$nextPageAvailable) {
                $nextPageAvailable1 = $count2 > ($position + $items_per_group) ? true : false;
            }

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
                    $q['Post']['likecount'] = sizeof($q['Like']);
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
       /*pr($res);
        pr($resu);die;*/
        $this->set('result111', $res);
        $this->set('result', $resu);

        $this->set('nextpageavailable', $nextPageAvailable);

        $this->set('login_userid', $loginUser);
        $this->set('current_userid', $userId);
    }



    public function autoload_process_supported() {  
       
        $post_sid = Array();
        Configure::write('debug', 0);
        $this->layout = "ajax";
        $this->loadModel('Support');
        $this->loadModel('Comment');
        $this->loadModel('Share');
        $this->loadModel('Like');

        $loginUser = $this->Auth->User('id');
        if (isset($this->request->data["current_userid"])) {
            $userId = $this->request->data["current_userid"];
        } else {
            $userId = $this->Auth->User('id');
        }






      $arr1[] = 1;
      $fid[] = 1; 
      $supporting = false;
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

            // users supports me
           /* $supportlist1_old = $this->Support->find('all', array('conditions' => array(
                    'AND' => array('Support.touserid' => $userId, 'Support.block' => 0)
                ), 'fields' => array('Support.user_id'), 'recursive' => -1));
            
             // users supports me
            $supportlist1 = $this->Support->find('all', array('conditions' => array(
                    'AND' => array('Support.touserid' => $this->Auth->user('id'), 'Support.block' => 0)
                ), 'fields' => array('Support.user_id'), 'recursive' => -1));*/
          
        }
       
        //$arr1[] = $userId;

       }/*else if(isset( $_POST['public'] ) &&  $_POST['public'] === "1"){

       $public_profile_user = $this->User->find('all', array('fields'=>array('id'),'contain'=>false,'conditions' => array(
                                 'AND'=>array(
                                 'User.profile_status' => 0,
                                 'User.id <>'=>$this->Auth->User('id')
                                 )
                              ), 'order' => array('User.id' => 'DESC')));


       if ( $public_profile_user && count( $public_profile_user ) > 0 ) {
                foreach ($public_profile_user as $public_profile) {
                    $arr1[] = $public_profile['User']['id'];
                }
            }
       $arr1[] = $loginUser;
      

       }else{

           $arr1[] = $userId;  
           $this->set('welcome_come',1);
       }  */




      /*  if (!$supporting && $userId != $loginUser) { // user and login users are diffrent and not $supporting
            $arr1[] = -1;
        } else { // assign invaid user id
            $arr1[] = -1;
        }*/
//debug($arr1);
        /* ===========list all friend id============== */





        $allusr = $this->User->find('all', array('conditions' => array('AND' => array('User.id' => $arr1)), 'fields' => array('User.id'), 'recursive' => 0));
        foreach ($allusr as $allusrs) {
            $fid[] = $allusrs['User']['id'];
        }
        //array_unique($fid);
        // in query works with array count higher > 1 so assign invaid user id
       /* if (sizeof($fid) == 1) { // if array count is 1 add one invalid entry
            $fid[] = -1;
        } elseif (sizeof($fid) == 0) { // if array count is 2 add two invalid entry
            $fid[] = -1;
            $fid[] = -1;
        }*/

        $items_per_group = isset($_POST['record_per_page']) ? $_POST['record_per_page'] : 5;
        //$group_number = filter_var($_POST["group_no"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
        $group_number = (int) $_POST["group_no"];
        
      


       $issameSendJust_supported = $this->Session->read('lastGroupNumber_supported');
       $increment = 0;
        if( $group_number === $issameSendJust_supported ){
        	$increment = 1;
        }


          $position = ( ( $group_number + $increment ) * $items_per_group);
        if (empty($position)) {
            setcookie("welcomepost", "0", time() - 3600);
        }

        $this->Session->write('lastGroupNumber_supported', $issameSendJust_supported);





     /*--------For Likes The Post------------------*/
      $post_support = [];
  $only_post_support  =  $this->Like->find('all',array(
                'fields'=>array('post_id'),
                'contain'=>false,'conditions'=>array(
                  'user_id'=>$this->Auth->User('id')
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
       
     
        $other=array(0,$this->Auth->user('id'));

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
                            'Post.share_with' => $this->Auth->user('id'),
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
//      debug($result);

        $nextPageAvailable = $count1 > ($items_per_group + $position) ? true : false;
        
        $resu = array();
        foreach ($result as $results) {          
            // debug($results['Share']);
            if ($results['User']['image']) {
                $results['User']['image'] = FULL_BASE_URL . $this->webroot . 'files/profile/' . $results['User']['image'];
            }
            $results['Post']['created'] = $this->Date->time_elapsed_string($results['Post']['created']);

            //$time = strtotime("$created");
            //$results['Post']['created']=$this->Timezone->Timezone_ctime($time);
            
            if ($results['Post']['ref_id'] != '0') {
                $tu = $this->User->find('first', array('conditions' => array('User.id' => $results['Post']['ref_id']), 'fields' => array('firstname', 'lastname'), 'recursive' => 0));
//                debug($tu);
                $results['Post']['refername'] = ucfirst($tu['User']['firstname']) . " " . ucfirst($tu['User']['lastname']);
            }
            if ($results['Like']) {
                $results['Post']['likecount'] = sizeof($results['Like']);
            } else {
                $results['Post']['likecount'] = 0;
            }
            if ($results['Comment']) {
                $results['Post']['commentcount'] = sizeof($results['Comment']);
            } else {
                $results['Post']['commentcount'] = 0;
            }
            
             $this->loadModel('PostView');
            $all_data=$this->PostView->find('count',array('conditions' => array('PostView.post_id' => $results['Post']['id'])));
              
             
                $results['Post']['view_count'] =$all_data;
            
            
           $comment_count = count($results['Comment']);
           for($i=0; $i<$comment_count; $i++){
               $created = $results['Comment'][$i]['created'];
                $time = strtotime($created);
                // $results['Comment'][$i]['created'] = $this->Timezone->Timezone_ctime($time);
                 $results['Comment'][$i]['created'] = $this->getTimeDiffrence($time);
           }

            //debug($cmnt);
            foreach ($results['Share'] as $share) {
                $post_sid[] .= $share['post_id'];
                $shared_post = $this->Post->find('first', array('conditions' => array('Post.id' => $post_sid), 'recursive' => 1));
                $shares[] = $shared_post;
            }
            $resu[] = $results;
        }
//        debug($resu);
        $res = array();
        if ($supporting || $userId == $loginUser) {
            $count2 = $this->Share->find('count', array('conditions' => array('OR' => array('Share.user_id' => $userId, 'Share.share_with' => $userId))));
            $qery = $this->Share->find('all', array('conditions' => array('OR' => array('Share.user_id' => $userId, 'Share.share_with' => $userId)), 'limit' => $items_per_group, 'offset' => $position,));
            if (!$nextPageAvailable) {
                $nextPageAvailable1 = $count2 > ($position + $items_per_group) ? true : false;
            }

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
                    $q['Post']['likecount'] = sizeof($q['Like']);
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
       /*pr($res);
        pr($resu);die;*/
        $this->set('result111', $res);
        $this->set('result', $resu);

        $this->set('nextpageavailable', $nextPageAvailable);

        $this->set('login_userid', $loginUser);
        $this->set('current_userid', $userId);
    }


    public function autoload_process() {  
       
        $post_sid = Array();
        Configure::write('debug', 0);
        $this->layout = "ajax";
        $this->loadModel('Support');
        $this->loadModel('Comment');
        $this->loadModel('Share');
        $this->loadModel('Like');
        $isOtherUsersProfile = false;
        $loginUser = $this->Auth->User('id');
        if (isset($this->request->data["current_userid"])) {
            $userId = $this->request->data["current_userid"];
            $isOtherUsersProfile = true;
        } else {
            $userId = $this->Auth->User('id');
        }
      $arr1 = array();
      $supporting = false;
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

            // users supports me
            $supportlist1_old = $this->Support->find('all', array('conditions' => array(
                    'AND' => array('Support.touserid' => $userId, 'Support.block' => 0)
                ), 'fields' => array('Support.user_id'), 'recursive' => -1));
            
             // users supports me
            $supportlist1 = $this->Support->find('all', array('conditions' => array(
                    'AND' => array('Support.touserid' => $this->Auth->user('id'), 'Support.block' => 0)
                ), 'fields' => array('Support.user_id'), 'recursive' => -1));
          
        }
       
        //$arr1[] = $userId;

       }else if(isset( $_POST['public'] ) &&  $_POST['public'] === "1"){

       $public_profile_user = $this->User->find('all', array('fields'=>array('id'),'contain'=>false,'conditions' => array(
                                 'AND'=>array(
                                 'User.profile_status' => 0,
                                 'User.id <>'=>$this->Auth->User('id')
                                 )
                              ), 'order' => array('User.id' => 'DESC')));


       if ( $public_profile_user && count( $public_profile_user ) > 0 ) {
                foreach ($public_profile_user as $public_profile) {
                    $arr1[] = $public_profile['User']['id'];
                }
            }
       $arr1[] = $loginUser;

       }else{

           $arr1[] = $userId;  
           $this->set('welcome_come',1);
       }  




        if (!$supporting && $userId != $loginUser) { // user and login users are diffrent and not $supporting
            $arr1[] = -1;
        } else { // assign invaid user id
            $arr1[] = -1;
        }
//debug($arr1);
        /* ===========list all friend id============== */
        $allusr = $this->User->find('all', array('conditions' => array('AND' => array('User.id IN' => $arr1)), 'fields' => array('User.id'), 'recursive' => 0));
        foreach ($allusr as $allusrs) {
            $fid[] = $allusrs['User']['id'];
        }
        //array_unique($fid);
        // in query works with array count higher > 1 so assign invaid user id
        if (sizeof($fid) == 1) { // if array count is 1 add one invalid entry
            $fid[] = -1;
        } elseif (sizeof($fid) == 0) { // if array count is 2 add two invalid entry
            $fid[] = -1;
            $fid[] = -1;
        }

        $items_per_group = isset($_POST['record_per_page']) ? $_POST['record_per_page'] : 5;
        //$group_number = filter_var($_POST["group_no"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
        $group_number = (int) $_POST["group_no"];

       
        $issameSendJust = $this->Session->read('lastGroupNumber');
        
        $increment = 0; 
        if( $group_number === $issameSendJust && $group_number != 0 ){
        	$increment = 1;
        }

        $this->Session->write('lastGroupNumber', $group_number);
        
        $position = ( ( $group_number + $increment ) * $items_per_group );
        if (empty($position)) {
            setcookie("welcomepost", "0", time() - 3600);
        }

        $this->loadModel('Post');
        $count1 = $this->Post->find('count', array(
            'conditions' => array('AND' => array('Post.user_id IN' => $fid
            	//'Post.status' => '1'
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
        /*$test = $this->Post->find('all',array(
            'conditions'=>array(
                "AND"=>array(
                    "OR"=>array(
                        'Post.share_with = Post.user_id',
                        'Post.share_with'=>$this->Auth->user('id'),
                    ),
                    //'Post.share_with'=>$fid,
                    'Post.status' => '1'
            ))
            ));*/
       // debug($test);
//        $shared = array();
//        foreach($test as $t){
//            array_push($shared, $t['Post']['user_id']);
//        }
        $other=array(0,$this->Auth->user('id'));
//        $shared = array_merge($shared,$other);
//        $shared = array_unique($shared);
//        debug($shared);
      //  debug($fid);


 if( $count1 == 1 && $_POST["group_no"] == 0){
        $position = 0;
         
     }

    
        
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
                            'Post.share_with' => $this->Auth->user('id'),
                            //'Post.share_with ==' => 'Post.user_id'
                        ),
                        'Post.share_with' => $other,
                        'Post.status' => '1'
                    )
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
//      debug($result);

        $nextPageAvailable = $count1 > ($items_per_group + $position) ? true : false;
        
        $resu = array();
        foreach ($result as $results) {          
            // debug($results['Share']);
            if ($results['User']['image']) {
                $results['User']['image'] = FULL_BASE_URL . $this->webroot . 'files/profile/' . $results['User']['image'];
            }
            $results['Post']['created'] = $this->Date->time_elapsed_string($results['Post']['created']);

            //$time = strtotime("$created");
            //$results['Post']['created']=$this->Timezone->Timezone_ctime($time);
            
            if ($results['Post']['ref_id'] != '0') {
                $tu = $this->User->find('first', array('conditions' => array('User.id' => $results['Post']['ref_id']), 'fields' => array('firstname', 'lastname'), 'recursive' => 0));
//                debug($tu);
                $results['Post']['refername'] = ucfirst($tu['User']['firstname']) . " " . ucfirst($tu['User']['lastname']);
            }
            if ($results['Like']) {
                $results['Post']['likecount'] = sizeof($results['Like']);
            } else {
                $results['Post']['likecount'] = 0;
            }
            if ($results['Comment']) {
                $results['Post']['commentcount'] = sizeof($results['Comment']);
            } else {
                $results['Post']['commentcount'] = 0;
            }
            
             $this->loadModel('PostView');
            $all_data=$this->PostView->find('count',array('conditions' => array('PostView.post_id' => $results['Post']['id'])));
              
             
                $results['Post']['view_count'] =$all_data;
            
            
           $comment_count = count($results['Comment']);
           for($i=0; $i<$comment_count; $i++){
               $created = $results['Comment'][$i]['created'];
                $time = strtotime($created);
                // $results['Comment'][$i]['created'] = $this->Timezone->Timezone_ctime($time);
                 $results['Comment'][$i]['created'] = $this->getTimeDiffrence($time);
           }

            //debug($cmnt);
            foreach ($results['Share'] as $share) {
                $post_sid[] .= $share['post_id'];
                $shared_post = $this->Post->find('first', array('conditions' => array('Post.id' => $post_sid), 'recursive' => 1));
                $shares[] = $shared_post;
            }
            $resu[] = $results;
        }
//        debug($resu);
        $res = array();
        if ($supporting || $userId == $loginUser) {
            $count2 = $this->Share->find('count', array('conditions' => array('OR' => array('Share.user_id' => $userId, 'Share.share_with' => $userId))));
            $qery = $this->Share->find('all', array('conditions' => array('OR' => array('Share.user_id' => $userId, 'Share.share_with' => $userId)), 'limit' => $items_per_group, 'offset' => $position,));
            if (!$nextPageAvailable) {
                $nextPageAvailable1 = $count2 > ($position + $items_per_group) ? true : false;
            }

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
                    $q['Post']['likecount'] = sizeof($q['Like']);
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



        if( count( $resu ) === 0 && $isOtherUsersProfile ){
        $remoteUser = $this->User->find('first',array('contain'=>false,'fields'=>array('created'),'conditions'=>array('User.id'=>$userId)));
         $this->set("remoteUser",$remoteUser);
         $this->set('remoteUserProfile',1);
         
       }


       
       // pr($res);
       // pr($resu);die;
        $this->set('result111', $res);
        $this->set('result', $resu);

        $this->set('nextpageavailable', $nextPageAvailable);

        $this->set('login_userid', $loginUser);
        $this->set('current_userid', $userId);
    }

    public function comnttect() {
        $this->layout = 'ajax';
        $this->loadModel('Comment');
        $this->loadModel('Post');
        $this->loadModel('User');
        $data = $this->Comment->find('all', array('conditions' => array(), 'order' => array('Comment.id DESC'), 'limit' => 3, 'contain' => array('User.Post')));
        debug($data);
        exit;
    }

    public function hidepost() {
        $this->loadModel('Post');
        $this->layout = "ajax";
        configure::write('debug',0);
        $post_id = $_POST['post_id'];
        $obj = $this->Post->updateAll(
                array('status' => '0'), array('Post.id' => $post_id)
        );
        $this->set('obj', $obj);
        $this->render('ajax');
    }

    public function editpost() {
        $this->loadModel('Post');
        $this->layout = "ajax";
        configure::write('debug', 0);
        $post_id = $_POST['post_id'];
        $post = $_POST['posts'];
        $this->Post->updateAll(
                array('post' => "'$post'"), array('Post.id' => $post_id)
        );
        $posts = $this->Post->find('first', array('conditions' => array('Post.id' => $post_id)));
        $obj = $posts['Post']['post'];
        $this->set('obj', $obj);
        $this->render('ajax1');
    }

    public function editcomment() {

        $this->loadModel('Comment');

        $this->layout = "ajax";

        configure::write('debug', 0);

        $comment_id = $_POST['comment_id'];

        $comment = $_POST['comment'];

        $this->Comment->updateAll(
                array('comment' => "'$comment'"), array('Comment.id' => $comment_id)
        );

        $posts = $this->Comment->find('first', array('conditions' => array('Comment.id' => $comment_id)));

        $obj = $posts['Comment']['comment'];

        $this->set('obj', $obj);

        $this->render('ajax1');
    }

    public function autosearch() {
        $this->loadModel('Support');
        $this->layout = "ajax";
        configure::write('debug', 0);
        $keyword = $_POST['keyword'];
        $obj = $this->ser->find('all', array('joins' => array(
                array('table' => 'supports',
                    'alias' => 's',
                    'type' => 'left',
                    'foreignKey' => false,
                    'conditions' => array('AND' => array('User.firstname LIKE' => '%' . $keyword . '%', 'User.id = s.touserid'), 'NOT' => array('User.id = s.user_id'))
                )
            ),
            'conditions' => array('s.user_id' => $this->Auth->user('id'))
        ));
        //$posts = $this->Comment->find('first', array('conditions' => array('Comment.id' => $comment_id)));			
        //$obj = $posts['Comment']['comment'];
        //debug($obj);
        $this->set('obj', $obj);
        $this->render('ajax1');
    }

    public function autoonline() {
        $this->loadModel('Online');
        $this->layout = "ajax";
        configure::write('debug', 0);
        $keyword = $_POST['keyword'];
        $obj = $this->Online->find('all', array(
            "conditions" => array('AND' => array('Online.user_id !=' => $this->Auth->User('id'), "OR" => array(
                        "Online.firstname LIKE" => $keyword . "%",
                        "Online.lastname LIKE" => $keyword . "%",
                        "Online.state LIKE" => $keyword . "%",
                        "Online.city LIKE" => $keyword . "%"
                    )
                ))
                )
        );
        $this->set('obj', $obj);
        $this->render('ajax1');
    }

    public function autosearchsds() {
        $this->loadModel('Support');
        $this->layout = "ajax";
        configure::write('debug', 0);
        $keyword = $_POST['keyword'];
        $obj = $this->User->find('all', array(
            "conditions" => array("OR" => array(
                    "User.firstname LIKE" => "%" . $keyword . "%",
                    "User.lastname LIKE" => "%" . $keyword . "%",
                    "User.email LIKE" => "%" . $keyword . "%",
                    "User.image LIKE" => "%" . $keyword . "%",
                    "User.created LIKE" => "%" . $keyword . "%",
                    "User.country LIKE" => "%" . $keyword . "%",
                    "User.city_town LIKE" => "%" . $keyword . "%"
                )
            )
                )
        );
        $this->set('obj', $obj);
        $this->render('ajax1');
    }

    public function edit_profile($id = NULL) {
        $this->layout = '_inner';
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        }
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
                $this->Session->setFlash(__('The User has been saved'));
                $this->redirect(array('action' => 'edit_profile/' . $id));
            } else {
                $this->Session->setFlash(__('The User could not be saved. Please, try again.'));
                $this->redirect(array('action' => 'edit_profile/' . $id));
            }
        } else {
            $this->request->data = $this->User->read(null, $id);
        }
        $this->set('admin_edit', $this->User->find('first', array('conditions' => array('User.id' => $id))));
    }

    public function changepassword($id = NULL) {
        $this->layout = '_inner';
        Configure::write('debug', 0);
        if ($this->request->is('post')) {
            $password = AuthComponent::password($this->data['User']['old_password']);
            $em = $this->Auth->user('username');
            $pass = $this->User->find('first', array('conditions' => array('AND' => array('User.password' => $password, 'User.username' => $em))));
            if ($pass) {
                if ($this->data['User']['new_password'] != $this->data['User']['cpassword']) {
                    $this->Session->setFlash(__("New password and Confirm password field do not match"));
                } else {
                    $this->User->data['User']['password'] = $this->data['User']['new_password'];
                    $this->User->id = $pass['User']['id'];
                    if ($this->User->exists()) {
                        $pass['User']['password'] = $this->data['User']['new_password'];
                        if ($this->User->save()) {
                            $this->Session->setFlash(__("Password Updated"));
                            $this->redirect(array('controller' => 'users', 'action' => 'changepassword'));
                        }
                    }
                }
            } else {
                $this->Session->setFlash(__("Your old password did not match."));
            }
        }
        $usr = $this->User->find('first', array('conditions' => array('User.id' => $this->Auth->User('id')), 'fields' => array('User.id', 'User.profile_status', 'messageid', 'pciv_status', 'support_ststus','sound_notifications','color'), 'recursive' => 0));

        $usersett = $usr;
        $this->loadModel('Notification');
//        $setting=$this->Notification->find('all',array('conditions'=>array('Notification.touserid'=>$this->Auth->User('id')),
//            'contain'=>array('User'=>array('User.id','User.firstname','User.lastname','User.image'),
//                'Post'=>array('Post.id'),'Like')));
//        foreach($setting as $settings){
//            if($settings['Notification']['type']=='3'){
//                debug($setting);
//            }
//           
//        }

        $this->loadModel('Post');
        $setting = $this->Notification->find('all', array('conditions' => array('AND' => array('Notification.touserid' => $this->Auth->User('id'), 'Notification.status' => 0)), 'recursive' => -1));
//       debug($setting);
        foreach ($setting as $settings) {
            if ($settings['Notification']['post_id'] != '0') {
                $noti = $this->Post->find('first', array('conditions' => array('AND' => array('Post.id' => $settings['Notification']['post_id'],
                            'Post.user_id' => $settings['Notification']['user_id'])),
                    'contain' => array('User' => 'User.id', 'User.firstname', 'User.lastname', 'User.image'),
                    'fields' => array('id', 'post'), 'recursive' => 0));
//              debug($noti);
                if ($settings['Notification']['type'] == '0') {
                    if ($noti['User']['id'])
                        $settinglist[] = $noti['User']['firstname'] . " " . $noti['User']['lastname'] . " Like your Post " . $noti['Post']['post'];
                }
                if ($settings['Notification']['type'] == '1') {
                    if ($noti['User']['id'])
                        $settinglist[] = $noti['User']['firstname'] . " " . $noti['User']['lastname'] . " Unlike your Post " . $noti['Post']['post'];
                }
                if ($settings['Notification']['type'] == '2') {
                    if ($noti['User']['id'])
                        $settinglist[] = $noti['User']['firstname'] . " " . $noti['User']['lastname'] . " Comment your Post " . $noti['Post']['post'];
                }
            }if ($settings['Notification']['support_id'] != '0') {
                $notii = $this->Support->find('first', array('conditions' => array('AND' => array('Support.id' => $settings['Notification']['support_id'],
                            'Support.user_id' => $settings['Notification']['user_id'])),
                    'contain' => array('User' => 'User.id', 'User.firstname', 'User.lastname', 'User.image'),
                    'fields' => array('id', 'user_id', 'status', 'block'), 'recursive' => 0));
                if ($settings['Notification']['type'] == '3') {
                    if ($notii['User']['id'])
                        $settinglist[] = $notii['User']['firstname'] . " " . $notii['User']['lastname'] . " Support you";
                }
                if ($settings['Notification']['type'] == '4') {
                    if ($notii['User']['id'])
                        $settinglist[] = $notii['User']['firstname'] . " " . $notii['User']['lastname'] . " Unsupport you";
                }
                if ($settings['Notification']['type'] == '5') {
                    if ($notii['User']['id'])
                        $settinglist[] = $notii['User']['firstname'] . " " . $notii['User']['lastname'] . " Blocked you";
                }
                if ($settings['Notification']['type'] == '6') {
                    if ($notii['User']['id'])
                        $settinglist[] = $notii['User']['firstname'] . " " . $notii['User']['lastname'] . " Unblocked you";
                }
                if ($settings['Notification']['type'] == '7') {
                    if ($notii['User']['id'])
                        $settinglist[] = $notii['User']['firstname'] . " " . $notii['User']['lastname'] . " Delete you from Support";
                }
            }
//                debug($settinglist);
        }
//        debug($setting);
        $this->set(compact('usersett', 'settinglist'));
    }

    public function firstswitch() {
        $this->layout = 'ajax';
        $id = $this->Auth->User('id');
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid User'));
        }
        if ($this->request->is(array('post'))) {
            //debug($this->request->data);
            $color = $this->request->data['messageid'];
            //exit;
            $this->User->id = $id;
            // $this->User->saveField('messageid', true);
            if ($this->User->saveField('messageid', $color)) {
                $response['msg'] = "yes";
            } else {
                $response['msg'] = "no";
            }
        }
        $this->set('response', $response);
        $this->render('/Common/ajax');
    }

    public function changepic($id = NULL) {
        $this->layout = '_inner';
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $one = $this->request->data['User']['image'];
            $image_name = $this->request->data['User']['image'] = date('dmHis') . $one['name'];
            if ($one['name'] != "") {
                $x = $this->User->read('image', $id);
                $x = 'files' . DS . 'profile' . DS . $x['User']['image'];
                // unlink($x);
                $pth = 'files' . DS . 'profile' . DS . $image_name;
                move_uploaded_file($one['tmp_name'], $pth);
            }
            if ($one['name'] == "") {
                $xc = $this->User->read('image', $id);
                $this->request->data['User']['image'] = $xc['User']['image'];
            }
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The User has been saved'));
                $this->redirect(array('controller' => 'users', 'action' => 'edit_profile/' . $id));
            } else {
                $this->Session->setFlash(__('The User could not be saved. Please, try again.'));
                $this->redirect(array('controller' => 'users', 'action' => 'edit_profile/' . $id));
            }
        } else {
            $this->request->data = $this->User->read(null, $id);
        }
        $this->set('editProfile', $this->User->find('first', array('conditions' => array('User.id' => $id))));
    }

    public function changecover($id = NULL) {
        $this->layout = '_inner';
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid User'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $one = $this->request->data['User']['coverphoto'];
            $image_name = $this->request->data['User']['coverphoto'] = date('dmHis') . $one['name'];
            if ($one['name'] != "") {
                $x = $this->User->read('coverphoto', $id);
                $x = 'files' . DS . 'coverphoto' . DS . $x['User']['coverphoto'];
                // unlink($x);
                $pth = 'files' . DS . 'coverphoto' . DS . $image_name;
                move_uploaded_file($one['tmp_name'], $pth);
            }
            if ($one['name'] == "") {
                $xc = $this->User->read('coverphoto', $id);
                $this->request->data['User']['coverphoto'] = $xc['User']['coverphoto'];
            }
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The User has been saved'));
                $this->redirect(array('controller' => 'users', 'action' => 'edit_profile/' . $id));
            } else {
                $this->Session->setFlash(__('The User could not be saved. Please, try again.'));
                $this->redirect(array('controller' => 'users', 'action' => 'edit_profile/' . $id));
            }
        } else {
            $this->request->data = $this->User->read(null, $id);
        }
        $this->set('editProfile', $this->User->find('first', array('conditions' => array('User.id' => $id))));
    }

    public function step1($id = NULL) {
        $this->layout = '_inner';
        $this->User->id = $id;
        if (!$this->User->exists()) {
            return $this->redirect(array('controller' => 'users', 'action' => 'notfound'));
//            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            //debug($this->request->data);exit;
            $cntry = $this->request->data['User']['country'];
            $cnt = $this->Location->find('first', array('conditions' => array('Location.location_id' => $cntry)));
            $this->request->data['User']['country'] = $cntry; // $cnt['Location']['name'];

            if ($this->request->data['User']['state'] != '') {
                $stat = $this->request->data['User']['state'];
                $st = $this->Location->find('first', array('conditions' => array('Location.location_id' => $stat)));
                $this->request->data['User']['state'] = $stat; // $st['Location']['name'];
            }
            if ($this->request->data['User']['city'] != '') {
                $cty = $this->request->data['User']['city'];
                $ct = $this->Location->find('first', array('conditions' => array('Location.location_id' => $cty)));
                $this->request->data['User']['city'] = $cty; // $ct['Location']['name'];
            }
            $this->request->data['User']['status'] = 1; 
            $this->User->query("update users set signup_complete='1' where id=$id");
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved.'));
                return $this->redirect(array('controller' => 'users', 'action' => 'step2' . '/' . $id));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
                return $this->redirect(array('controller' => 'users', 'action' => 'step1' . '/' . $id));
            }
        } else {
            $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
        }
        $this->set('description', $sdds = $this->User->find('first', array('conditions' => array('User.id' => $id))));
    }

    public function step2($id = NULL) {
       // debug($id); exit;

        $this->layout = '_inner';

        $this->User->id = $id;

        if (!$this->User->exists()) {
            return $this->redirect(array('controller' => 'users', 'action' => 'notfound'));
//            throw new NotFoundException(__('Invalid user'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            
            $this->request->data['User']['email_varification']=0;
            $one = $this->request->data['User']['image'];

            $image_name = $this->request->data['User']['image'] = date('dmHis') . $one['name'];

            if ($one['name'] != "") {

                $x = $this->User->read('image', $id);

                $x = 'files' . DS . 'profile' . DS . $x['User']['image'];

                //unlink($x);

                $pth = 'files' . DS . 'profile' . DS . $image_name;

                move_uploaded_file($one['tmp_name'], $pth);
            }

            if ($one['name'] == "") {

                $xc = $this->User->read('image', $id);

                $this->request->data['User']['image'] = $xc['User']['image'];
            }

            if ($this->User->save($this->request->data)) {

                $this->Session->setFlash(__('The user has been saved.'));

                return $this->redirect(array('controller' => 'users', 'action' => 'step2' . '/' . $id));
            } else {

                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));

                return $this->redirect(array('controller' => 'users', 'action' => 'step2' . '/' . $id));
            }
        } else {

            $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
        }



        $this->set('description', $sdds = $this->User->find('first', array('conditions' => array('User.id' => $id))));

        //debug($sdds);exit;
    }

    public function step3($id = NULL) {

        $this->layout = '_inner';

        $this->User->id = $id;

        if (!$this->User->exists()) {
            return $this->redirect(array('controller' => 'users', 'action' => 'notfound'));
//            throw new NotFoundException(__('Invalid user'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {

            $one = $this->request->data['User']['coverphoto'];

            $image_name = $this->request->data['User']['coverphoto'] = date('dmHis') . $one['name'];

            if ($one['name'] != "") {

                $x = $this->User->read('coverphoto', $id);

                $x = 'files' . DS . 'coverphoto' . DS . $x['User']['coverphoto'];

                //unlink($x);

                $pth = 'files' . DS . 'coverphoto' . DS . $image_name;

                move_uploaded_file($one['tmp_name'], $pth);
            }

            if ($one['name'] == "") {

                $xc = $this->User->read('coverphoto', $id);

                $this->request->data['User']['coverphoto'] = $xc['User']['coverphoto'];
            }

            if ($this->User->save($this->request->data)) {

                $this->Session->setFlash(__('The user has been saved.'));

                return $this->redirect(array('controller' => 'users', 'action' => 'step3' . '/' . $id));
            } else {

                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));

                return $this->redirect(array('controller' => 'users', 'action' => 'step3' . '/' . $id));
            }
        } else {

            $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
        }



        $this->set('description', $sdds = $this->User->find('first', array('conditions' => array('User.id' => $id))));
    }

    /*public function step4($id = NULL) {
        $this->layout = '_inner2';
        $this->User->id = $id;
        $this->loadModel('Invitation');
        if (!$this->User->exists()) {
            return $this->redirect(array('controller' => 'users', 'action' => 'notfound'));
//            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('description', $sdds = $this->User->find('first', array('conditions' => array('User.id' => $id))));
    }*/
 public function step4($id = NULL) {
        Configure::write('debug',0);
        /*$this->layout = '_inner2';
        $this->User->id = $id;
        $this->loadModel('Invitation');
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('description', $sdds = $this->User->find('first', array('conditions' => array('User.id' => $id))));*/

        $this->layout = '_inner2';
        $this->User->id = $id;
        $this->loadModel('Invitation');
        $returnUrl = $this->Session->read('returnUrl');

       if( isset( $returnUrl ) && !empty( $returnUrl ) )
       {



                
               $isUSer = $this->User->find('first',array('conditions'=>array(
                     'User.id' => $id
                    ),
                'contain'=>false)
                );

              $new_hash = sha1($isUSer['User']['email'] . rand(0, 100));

              

      $this->User->updateAll( array('tokenhash' => "'$new_hash'","status"=>1),array('User.id' => $id));

                if ( ! empty( $isUSer ) && count( $isUSer['User'] ) > 0) {


                 foreach ($isUSer as $key => $value) {


                      $filename = $_SERVER['DOCUMENT_ROOT'].$this->webroot.'app/webroot/files/profile/'.$value['image'];

                      if ($value['image'] && file_exists ( $filename )) {    
                        $isUSer[$key]['image'] = FULL_BASE_URL . $this->webroot . 'files/profile/' . $value['image'];
                    }else{
                        $isUSer[$key]['image'] = "http://www.buira.net/assets/images/shared/default-profile.png";
                    }

                        
                        $isUSer[$key]['coverphoto'] = FULL_BASE_URL . $this->webroot . 'files/coverphoto/' . $value['coverphoto'];
                         



                          $isUSer[$key]['tokenhash'] = $new_hash;
                 
                 }
                 unset($isUSer['User']['password']);
                 $userInformation =$isUSer;

                 

                 if( count( $userInformation ) > 0 && !empty( $userInformation ) ) {    
                    $response['Error'] =false;
                    $response['SuccessCode'] = 200;
                    //$response['returnUrl'] = trim($returnUrl);
                    $response['successMessage'] = "User logged with mysponsers.com";
                    $response['userInformation'] = $userInformation;
                   /* echo json_encode($response);
                    header ("Location: $returnUrl");*/
                    /*----Send curl data ----------------------*/
                    $curl = curl_init($returnUrl);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($response));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $response11 = curl_exec($curl);
                    $json = json_decode($response11, true);
                   
                    curl_close($curl);
                    $this->Session->delete('returnUrl');
                     if( $json['status'] === 'false' )
                    {
                    $response1['Error'] =false;
                    $response1['SuccessCode'] = 200;
                    $response1['redirecturl'] = $json['Page'];
                    $response1['tokenhash'] = ''; 
                    $response1['message'] = $json['message'];
                    }else{
                    $response1['Error'] =false;
                    $response1['SuccessCode'] = 200;
                    $response1['redirecturl'] = $json['Page'];
                    $response1['tokenhash'] =$new_hash;
                    $response1['message'] = $json['message'];
                    } 
                   // echo $json['Page']."?tokenhash =".$response1['tokenhash']."&message =". $json['message'];die;
                    $this->redirect($json['Page']."?tokenhash=".$response1['tokenhash']."&message=".$json['message']);
                }
            }
           /* echo "You success signed up with mysponsers.please wait you will redirect in a moment..";
            sleep(5); 
            $this->redirect($returnUrl);*/

       }else{

            if (!$this->User->exists()) {
                throw new NotFoundException(__('Invalid user'));
            }
            $this->set('description', $sdds = $this->User->find('first', array('conditions' => array('User.id' => $id))));
        }
    }








    public function deleteuser($id = null) {

        $this->User->id = $id;

        if (!$this->User->exists()) {

            throw new NotFoundException(__('Invalid user'));
        }

        $this->request->allowMethod('post', 'delete');

        if ($this->User->delete()) {

            $this->User->query("DELETE FROM users WHERE id=$id");

            $this->Session->setFlash(__('The user has been deleted.'));

            return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
        } else {

            $this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(array('controller' => 'users', 'action' => 'index' . '/' . $id));
    }

    public function deletesign() {

//        $this->layout = 'ajax';

        $this->User->id = $this->request->data['id'];
//        $id=233;
        $this->loadModel('Deleteuser');

//        if (!$this->User->exists()) {
//
//            throw new NotFoundException(__('Invalid User'));
//        }
        $this->User->recursive=0;
        $use=$this->User->find('first',array('conditions'=>array('User.id'=>$this->User->id)));
        if($use){
         $this->request->data['Deleteuser']['id']= $use['User']['id'];   $this->request->data['Deleteuser']['password']= $use['User']['password'];
         $this->request->data['Deleteuser']['role']= $use['User']['role']; $this->request->data['Deleteuser']['firstname']= $use['User']['firstname'];
         $this->request->data['Deleteuser']['lastname']= $use['User']['lastname']; $this->request->data['Deleteuser']['username']= $use['User']['username'];
         $this->request->data['Deleteuser']['email']= $use['User']['email'];  $this->request->data['Deleteuser']['image']= $use['User']['image'];
         $this->request->data['Deleteuser']['coverphoto']= $use['User']['coverphoto']; $this->request->data['Deleteuser']['country']= $use['User']['country'];
         $this->request->data['Deleteuser']['state']= $use['User']['state']; $this->request->data['Deleteuser']['city']= $use['User']['city'];
         $this->request->data['Deleteuser']['phone']= $use['User']['phone']; $this->request->data['Deleteuser']['cell_no']= $use['User']['cell_no'];
         $this->request->data['Deleteuser']['home']= $use['User']['home']; $this->request->data['Deleteuser']['sex']= $use['User']['sex'];
         $this->request->data['Deleteuser']['dob']= $use['User']['dob']; $this->request->data['Deleteuser']['born_address']= $use['User']['born_address'];
         $this->request->data['Deleteuser']['raised_child']= $use['User']['raised_child'];
         $this->request->data['Deleteuser']['where_live']= $use['User']['where_live']; $this->request->data['Deleteuser']['workplace_company']= $use['User']['workplace_company'];
         $this->request->data['Deleteuser']['where_lived']= $use['User']['where_lived']; $this->request->data['Deleteuser']['addict_alco_type']= $use['User']['addict_alco_type'];
         $this->request->data['Deleteuser']['where_visited']= $use['User']['where_visited'];  $this->request->data['Deleteuser']['addtimezone']= $use['User']['addtimezone'];
         $this->request->data['Deleteuser']['family_member']= $use['User']['family_member']; $this->request->data['Deleteuser']['time_addict_alco']= $use['User']['time_addict_alco'];
         $this->request->data['Deleteuser']['relation_ship']= $use['User']['relation_ship']; $this->request->data['Deleteuser']['highschool']= $use['User']['highschool'];
         $this->request->data['Deleteuser']['college']= $use['User']['college'];  $this->request->data['Deleteuser']['company']= $use['User']['company'];
         $this->request->data['Deleteuser']['position']= $use['User']['position'];  $this->request->data['Deleteuser']['city_town']= $use['User']['city_town'];
         $this->request->data['Deleteuser']['designation']= $use['User']['designation'];  $this->request->data['Deleteuser']['color']= $use['User']['color'];
         $this->request->data['Deleteuser']['workplace_description']= $use['User']['workplace_description']; $this->request->data['Deleteuser']['photo_status']= $use['User']['photo_status'];
         $this->request->data['Deleteuser']['college_passout']= $use['User']['college_passout']; $this->request->data['Deleteuser']['professional_skill']= $use['User']['professional_skill'];
         $this->request->data['Deleteuser']['current_city_town']= $use['User']['current_city_town'];  $this->request->data['Deleteuser']['highschool_passout']= $use['User']['highschool_passout'];
         $this->request->data['Deleteuser']['your_places']= $use['User']['your_places']; $this->request->data['Deleteuser']['yourhometown']= $use['User']['yourhometown'];
         $this->request->data['Deleteuser']['about_you']= $use['User']['about_you']; $this->request->data['Deleteuser']['meeting']= $use['User']['meeting'];
         $this->request->data['Deleteuser']['favorite_quotes']= $use['User']['favorite_quotes'];  $this->request->data['Deleteuser']['motto']= $use['User']['motto'];
         $this->request->data['Deleteuser']['status']= $use['User']['status'];  $this->request->data['Deleteuser']['profile_status']= $use['User']['profile_status'];
         $this->request->data['Deleteuser']['messageid']= $use['User']['messageid']; $this->request->data['Deleteuser']['pciv_status']= $use['User']['pciv_status'];
         $this->request->data['Deleteuser']['support_ststus']= $use['User']['support_ststus']; $this->request->data['Deleteuser']['photo_status']= $use['User']['photo_status'];
         $this->request->data['Deleteuser']['photo_status_photo']= $use['User']['photo_status_photo'];  $this->request->data['Deleteuser']['sound_notifications']= $use['User']['sound_notifications'];
         $this->request->data['Deleteuser']['tokenhash']= $use['User']['tokenhash'];  $this->request->data['Deleteuser']['last_login']= $use['User']['last_login'];
         $this->request->data['Deleteuser']['tz']= $use['User']['tz']; $this->request->data['Deleteuser']['email_varification']= $use['User']['email_varification'];
         if(!empty(@$use['User']['signup_complete'])){
         $this->request->data['Deleteuser']['signup_complete']= $use['User']['signup_complete'];
         }else{ $this->request->data['Deleteuser']['signup_complete']='1'; } $this->request->data['Deleteuser']['delete_reason']= '1';
         $this->Deleteuser->create();
         $this->Deleteuser->save($this->request->data);
        }
//        
//        debug($use);
//        debug($this->User->id); exit;
        $this->request->allowMethod('post', 'delete');

        if ($this->User->delete()) {

            $this->set("res", array(
                'r' => 1
            ));

            $this->response->type('json');

            $this->render("/Common/ajax", "ajax");
        }
    }
     private function getTimeDiffrence( $created = null ) {
       // configure::write('debug', 2);
        /* http://stackoverflow.com/questions/365191/how-to-get-time-difference-in-minutes-in-php/365214#365214*/
        $results = 0;
        $start_date = new DateTime(date('y-m-d H:i:s',$created));
        $since_start = $start_date->diff(new DateTime(date('y-m-d H:i:s')));
            if( $since_start->d > 0) {
               $results = $since_start->d.' days Ago';
               return $results;
              }else  if( $since_start->h > 0) {
               $results = $since_start->h.' Hours Ago';
               return $results;
              }else  if( $since_start->i > 0) {
               $results = $since_start->i.' Mins Ago';
               return $results;
              }else  if( $since_start->s > 0) {
               $results = 'Just Now';
               return $results;
              }else{
                $results = 'Just Now';
              }
          return $results;
      }

    public function commentpost() {
        configure::write('debug', 0);
        $this->layout = "ajax";
        $this->loadModel('Comment'); 
        $this->request->data['Comment']['comment'] = trim($this->request->data['Comment']['comment']);
        $last_record = $this->Comment->find("first", array(
            "conditions" => array(
                "AND" => array(
                    "Comment.post_id" => $this->request->data['Comment']['post_id'],
                    "Comment.user_id" => $this->request->data['Comment']['user_id'],
                    "Comment.comment" => $this->request->data['Comment']['comment']
                )
            ),
            'order' => array('Comment.id Desc')
        ));
        if (!empty($last_record)) {
            if (($last_record['Comment']['post_id'] == $this->request->data['Comment']['post_id']) &&
                    ($last_record['Comment']['user_id'] == $this->Auth->user('id')) &&
                    ($last_record['Comment']['comment'] == $this->request->data['Comment']['comment'])
            ) {
                $response['post_id'] = $last_record['Comment']['post_id'];
                $response['count'] = $this->Comment->find('count', array('conditions' => array('Comment.post_id' => $last_record['Comment']['post_id']), 'fields' => array('Comment.id'), 'recursive' => 0));
                $response['comment'] = $last_record['Comment']['comment'];
                $response['user_img'] = $last_record['Comment']['user_img'];
                $response['id'] = $last_record['Comment']['id'];
                $created = $last_record['Comment']['created'];
                $time = strtotime($created);
                $response['created'] = $this->Timezone->Timezone_ctime($time);
                $response['sincetime'] = $this->getTimeDiffrence($time);
               // $response['created'] = $last_record['Comment']['created'];
                $response['success'] = "success";
                $response['duplicate'] = 1;
            }
        } else {
            $this->request->data['Comment']['email_notification']=0;
            $this->Comment->create();
            if ($this->Comment->save($this->request->data)) {
                $response['post_id'] = $this->request->data['Comment']['post_id'];
                $response['count'] = $this->Comment->find('count', array('conditions' => array('Comment.post_id' => $response['post_id']), 'fields' => array('Comment.id'), 'recursive' => 0));
                $response['comment'] = $this->request->data['Comment']['comment'];




                        /*For image and name Added by vipin chuahan----------------------*/

           $username = $this->User->find('first',array('contain'=>false,'conditions'=>array('id'=>$this->request->data['Comment']['user_id'])));

            $filename = $_SERVER['DOCUMENT_ROOT'].'/files/profile/'.$this->request->data['Comment']['user_img'];

             if ($this->request->data['Comment']['user_img'] && file_exists ( $filename )) { 

                $response['user_img'] =  host.'files/profile/' . $this->request->data['Comment']['user_img'];
            }else{

                $response['user_img'] = $this->webroot . 'inner/images/default-user-icon-profile.png';  
            }
           $response['username'] = $username['User']['firstname'];
           $response['userid'] =  $this->Auth->user('id');
             
   /*For image and name Added by vipin chuahan------------------------*/
               








                $response['id'] = $this->Comment->id;
                $comment = $this->Comment->find("first", array("conditions" => array("Comment.id" => $this->Comment->id), "recursive" => 1));
                $created = $comment['Comment']['created'];
                $time = strtotime($created);
                $response['created'] = $this->Timezone->Timezone_ctime($time);
                $response['sincetime'] = $this->getTimeDiffrence($time);
                //$response['created'] = date("F d, Y g:i A", strtotime("$created"));
                $response['success'] = "success";
                $response['duplicate'] = 0;
            } else {
                $response['success'] = "failed";
            }
        }
        echo json_encode($response);
        $this->render('ch');
    }







public function getAjaxProfilePhoto(){
	    $this->layout = "ajax";
        $page_no = $_POST['requestNo'];
        $limit = 4;
        $position = ( int ) ( $page_no * $limit ); 
        $this->loadModel("Gallery");
        $photos = $this->Gallery->find("all",array(
            'limit'=>$limit,
            'offset' => $position,
            'order' => array('Gallery.id DESC'),
            "conditions"=>array(
                "AND"=>array(
                    'Gallery.user_id'=>$this->Auth->user('id'),
                    'Gallery.type'=>"profile"
                )
            )
        ));
        $this->set("profile_photos",$photos);

}
public function getAjaxLandscapePhoto(){
        $this->layout = "ajax";
        $page_no = $_POST['requestNo'];
        $limit = 4;
        $position = ( int ) ( $page_no * $limit ); 
        $this->loadModel("Gallery");

        $landscape_photos = $this->Gallery->find("all",array(
                                'limit'=>$limit,
                                'offset' => $position,
                                'order' => array('Gallery.id DESC'),
                                "conditions"=>array(
                                    "AND"=>array(
                                        'Gallery.user_id'=>$this->Auth->user('id'),
                                        'Gallery.type'=>"landscape"
                                    )
                                )
                            ));
        $this->set("landscape_photos",$landscape_photos);

}

public function getAjaxOtherPhoto(){
        $this->layout = "ajax";
        $page_no = $_POST['requestNo'];
        $limit = 4;
        $position = ( int ) ( $page_no * $limit );
        $this->loadModel('Post'); 
        $lid = $this->Auth->user('id');
        $dataImage = $this->Post->find('all', array(
            'recursive' => 0,
            'limit'=>$limit,
            'offset' => $position,
            'conditions' => array(
                'AND' => array(
                    'Post.user_id' => $lid
                ),
                'OR' => array(
                    'Post.type' => array('image')
                )
            ),
            'group' => '`Post`.`photo`'
            
        ));
 
        $this->set('pdata', $dataImage);

}

public function getAjaxVideo(){
         $this->layout = "ajax";
         $page_no = $_POST['requestNo'];
         $limit = 3;
         $position = ( int ) ( $page_no * $limit );
         $this->loadModel('Post');
         $lid = $this->Auth->user('id');
         $dataVideo = $this->Post->find('all', array(
            'recursive' => 0,
            'limit'=>$limit,
            'offset' => $position, 
            'conditions' => array(
                'AND' => array(
                    'Post.user_id' => $lid
                ),
                'OR' => array(
                    'Post.type' => array('video')
                )
            ),
            'group' => 'photo'
        ));

        $this->set('videoData', $dataVideo);



}

public function photo() {

	//select `photo`,count( id ) as total_photo from posts where user_id = 1050 AND `type` = 'image' GROUP BY photo;
       /* Configure::write('debug',2); */
        $this->layout = '_inner';
        /*$this->loadModel('Post');
        $lid = $this->Auth->user('id');
        $dataVideo = $this->Post->find('all', array(
            'recursive' => 0,
            //'limit'=>3, 
            'conditions' => array(
                'AND' => array(
                    'Post.user_id' => $lid
                ),
                'OR' => array(
                    'Post.type' => array('video')
                )
            ),
            'group' => 'photo'
        ));

        $this->set('videoData', $dataVideo);*/
    }








    /*
  
  commented by vipin for media se file upload karne ke liye.

    public function photo() {
        $this->layout = '_inner';
        $this->loadModel('Post');
        $lid = $this->Auth->user('id');
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
        ));
        

        $this->set('pdata', $dataImage);
 
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
        $this->set('videoData', $dataVideo);


        
        
        $this->loadModel("Gallery");
        $photos = $this->Gallery->find("all",array(

            'contain' => false,
            'fields' => array(
                    'Gallery.id',
                    'Gallery.image'
                ),
            "conditions"=>array(
                "AND"=>array(
                    'Gallery.user_id'=>$this->Auth->user('id'),
                    'Gallery.type'=>"profile"
                )
            )
        ));
        $this->set("profile_photos",$photos);

        
        
        $landscape_photos = $this->Gallery->find("all",array(

                           'contain' => false,
                            'fields' => array(
                                    'Gallery.id',
                                    'Gallery.image'
                                ),
                                "conditions"=>array(
                                    "AND"=>array(
                                        'Gallery.user_id'=>$this->Auth->user('id'),
                                        'Gallery.type'=>"landscape"
                                    )
                                )
                            ));
         
        $this->set("landscape_photos",$landscape_photos);
         
    }*/


    /*

  Commented for optimization
    public function photo() {
        $this->layout = '_inner';
        $this->loadModel('Post');
        $lid = $this->Auth->user('id');
        $dataImage = $this->Post->find('all', array(
            'recursive' => 0,
            'conditions' => array(
                'AND' => array(
                    'Post.user_id' => $lid
                ),
                'OR' => array(
                    'Post.type' => array('image')
                )
            )
        ));

        $this->set('pdata', $dataImage);
//        $dataVideo=$this->Post->find('all',array('conditions'=>array('AND'=>array('Post.user_id'=>$lid)),'recursive'=>0));
        $dataVideo = $this->Post->find('all', array(
            'recursive' => 0,
            'conditions' => array(
                'AND' => array(
                    'Post.user_id' => $lid
                ),
                'OR' => array(
                    'Post.type' => array('video')
                )
            )
        ));
        $this->set('videoData', $dataVideo);
        
        //Profile photo's
        $this->loadModel("Gallery");
        $photos = $this->Gallery->find("all",array(
            "conditions"=>array(
                "AND"=>array(
                    'Gallery.user_id'=>$this->Auth->user('id'),
                    'Gallery.type'=>"profile"
                )
            )
        ));
        $this->set("profile_photos",$photos);
        
        //landscape photo
        $landscape_photos = $this->Gallery->find("all",array(
                                "conditions"=>array(
                                    "AND"=>array(
                                        'Gallery.user_id'=>$this->Auth->user('id'),
                                        'Gallery.type'=>"landscape"
                                    )
                                )
                            ));
        $this->set("landscape_photos",$landscape_photos);
         
    }*/

    public function profile($id = NULL) {
        $this->layout = '_inner';
        $loggedid = $this->Auth->user('id');
        $dataAbout = $this->User->find('first', array('conditions' => array('User.id' => $loggedid)));
        $this->set('userAbout', $dataAbout);

//        // countries
//        $this->loadModel('Country');
//        $countries = $this->Country->query('select * from countries  ORDER BY `name` asc ');
//        $this->set('countries', $countries);
    } 
    public function deletevideo($id = null) {

        $this->loadModel('Post');

        $this->Post->id = $id;

        if (!$this->Post->exists()) {

            throw new NotFoundException(__('Invalid Post'));
        }

        $this->request->allowMethod('post', 'delete');

        if ($this->Post->delete()) {

            $this->Post->query("DELETE FROM `posts` WHERE `posts`.`id` = $id");

            $this->Session->setFlash(__('The Post has been deleted.'));
        } else {

            $this->Session->setFlash(__('The Post could not be deleted. Please, try again.'));
        }

        return $this->redirect(array('controller' => 'users', 'action' => 'photo'));
    }
    
    public function deleteparticularimage($id = null,$image=null) {

        $this->loadModel('Post');

        $this->Post->id = $id;

        if (!$this->Post->exists()) {

            throw new NotFoundException(__('Invalid Post'));
        }else{
            $post = $this->Post->find("first",array("conditions"=>array(
                "Post.id"=>$id
            )));
             $photos = unserialize($post['Post']['photo']);
             if(($key = array_search($image, $photos)) !== false) {
                unset($photos[$key]);
            }
//            debug($photos);
            $serialize = serialize($photos);
            $this->Post->id=$id;
            $this->Post->saveField("photo",$serialize);
        }
        
//
//        $this->request->allowMethod('post', 'delete');
//
//        if ($this->Post->delete()) {
//
//            $this->Post->query("DELETE FROM `uneekart_chris`.`posts` WHERE `posts`.`id` = $id");
//
//            $this->Session->setFlash(__('The Post has been deleted.'));
//        } else {
//
//            $this->Session->setFlash(__('The Post could not be deleted. Please, try again.'));
//        }

        return $this->redirect(array('controller' => 'users', 'action' => 'photo'));
    }

    public function deactivatecover() {

        $this->layout = 'ajax';

        $this->User->id = $this->Auth->user('id');
        $this->loadModel('Gallery');
        $this->Gallery->id=$this->request->data['id'];
        $response= array();
        if($this->Gallery->exists()){
            $this->request->allowMethod('post', 'delete');
            $gallery = $this->Gallery->findById($this->Gallery->id);
             $imagePath = $_SERVER['DOCUMENT_ROOT']."/app/webroot/files/coverphoto/original/".$gallery['Gallery']['image'];
            $gallery_image = $gallery['Gallery']['image'];
            unlink("$imagePath"); // correct
            if ($this->Gallery->delete()) {
                $response['msg'] = "Image Deleted";
            }else{
                $response['msg'] = "Image not Deleted";
            }
        }

        if ($this->User->exists()) {

            $x = $this->User->save(array(
                'User' => array(
                    'photo_status' => '1'
                )
            ));

            $response['success'] = "success";
        } else {

            $response['success'] = "fialed";
        }

        $this->set('response', $response);

        $this->render('ajax3');
    }

    public function deactivatephoto() {

        $this->layout = 'ajax';
        
        $this->loadModel('Gallery');
        $this->Gallery->id=$this->request->data['id'];
        $response= array();
        if($this->Gallery->exists()){
            $this->request->allowMethod('post', 'delete');
            $gallery = $this->Gallery->findById($this->Gallery->id);
             $imagePath = $_SERVER['DOCUMENT_ROOT']."/app/webroot/files/profile/original/".$gallery['Gallery']['image'];
            $gallery_image = $gallery['Gallery']['image'];
            unlink("$imagePath"); // correct
            if ($this->Gallery->delete()) {
                $response['msg'] = "Image Deleted";
            }else{
                $response['msg'] = "Image not Deleted";
            }
        }
        
        

        $this->User->id = $this->Auth->user('id');

        if ($this->User->exists()) {

            $x = $this->User->save(array(
                'User' => array(
                    'photo_status_photo' => '1'
                )
            ));

            $response['success'] = "success";
        } else {

            $response['success'] = "fialed";
        }

        $this->set('response', $response);

        $this->render('ajax3');
    }


     public function getMOrePicks(){

        Configure::write('debug',2);
        

        $this->loadModel('Post');

        $dataVideo = $this->Post->find('all', array(
            'recursive' => -1,
            'limit'=>4,
            'page'=>$_GET['page_no'],
            'conditions' => array('AND' => array('Post.user_id' => $_GET['user_id']), 'OR' => array(
                    'Post.type' => array('image')
        ))));

        
       
        foreach ($dataVideo as $key => $value) {
            $dataVideo[$key]['Post']['photo'] = unserialize($dataVideo[$key]['Post']['photo']);
        }
        
        echo json_encode($dataVideo);die;


    }



  public function loadMoreVideo(){

        Configure::write('debug',2);
        

        $this->loadModel('Post');

        $dataVideo = $this->Post->find('all', array(
            'recursive' => -1,
            'limit'=>4,
            'page'=>$_GET['page_no'],
            'conditions' => array('AND' => array('Post.user_id' => $_GET['user_id']), 'OR' => array(
                    'Post.type' => array('video')
        ))));

        
       
        foreach ($dataVideo as $key => $value) {
            $dataVideo[$key]['Post']['photo'] = unserialize($dataVideo[$key]['Post']['photo']);
        }
        
        echo json_encode($dataVideo);die;


    }
    public function getAjaxSupporting(){


    	Configure::write('debug',0);
    	$this->layout = "ajax";
    	$page_no = $_POST['requestNo'];
    	//$page_no = 0;
    	$id = $_POST['remote_user_id'];
    	/*$id = 18226;*/
    	//$id = 681;
    	$limit = 6;
        $position = ( int ) ( $page_no * $limit ); 
        $this->loadModel('Support');
    	 $this->Support->recursive = -1;
        $suppoting = $this->Support->find('all', array('conditions' => array('Support.user_id' => $id), 'fields' => array('Support.touserid'),'limit' => $limit,'offset' => $position));
      // pr($suppoting);die;
        foreach ($suppoting as $invitelists_ing) {
            $email_ing[] = $invitelists_ing['Support']['touserid'];
        }

        $em1 = sizeof($email_ing);
        if ($em1 == 1)
            $email_ing[] = $id;
        if ($email_ing) {
            $allusring = $this->User->find('all', array('conditions' => array('User.id IN' => $email_ing), 'fields' => array('User.id', 'User.firstname', 'User.lastname', 'User.image', 'User.email', 'User.messageid'),
                'contain' => array('Support' => array('conditions' => array('Support.user_id' => $id),
                        'fields' => array('Support.id', 'Support.user_id', 'Support.touserid', 'Support.block'))), 'recursive' => -1));
            foreach ($allusring as $allusrs_ing) {
                $mutualing['Mutualing'][$allusrs_ing['User']['id']] = $this->Support->find('count', array('conditions' => array('OR' => array('Support.user_id' => $allusrs_ing['User']['id'], 'Support.touserid' => $allusrs_ing['User']['id'])), 'recursive' => -1));
            }
        }

        /* --------------suppotor---------------------- */

       
        $this->set(compact('mutualing', 'allusring'));



    }

      public function getAjaxSupporter(){
    	
    	Configure::write('debug',0);
    	$this->layout = "ajax";
    	$page_no = $_POST['requestNo'];
    	//$page_no = 0;
    	$id = $_POST['remote_user_id'];
    	/*$id = 18226;*/
    	//$id = 681;
    	$limit = 6;
        $position = ( int ) ( $page_no * $limit ); 
        $this->loadModel('Support');
    	 $this->Support->recursive = -1;
      
        /* --------------suppotor---------------------- */

        $invitelist = $this->Support->find('all', array('conditions' => array('Support.touserid' => $id), 'fields' => array('Support.user_id'),'limit' => $limit,'offset' => $position));
//       debug($invitelist); 
        foreach ($invitelist as $invitelists) {
            $email[] = $invitelists['Support']['user_id'];
        }
//       debug($email);
        $em = sizeof($email);
        if ($em == 1) {
            $email[] = $id;
        }
//          debug($em);
        if ($email) {
            $allusr = $this->User->find('all', array('conditions' => array('User.id IN' => $email), 'fields' => array('User.id', 'User.firstname', 'User.lastname', 'User.image', 'User.email', 'User.messageid'),
                'contain' => array('Support' => array('conditions' => array('Support.touserid' => $id),
                        'fields' => array('Support.id', 'Support.user_id', 'Support.touserid', 'Support.block'))), 'recursive' => -1));
            //debug($allusr);     
            foreach ($allusr as $allusrs) {
                $mutual['Mutual'][$allusrs['User']['id']] = $this->Support->find('count', array('conditions' => array('OR' => array('Support.user_id' => $allusrs['User']['id'], 'Support.touserid' => $allusrs['User']['id'])), 'recursive' => -1));
            }
        }
        
  
       
        $this->set(compact('allusr', 'mutual'));


    	
       }




    public function userview($id = NULL) {
        
        Configure::write('debug', 0);
        $this->layout = '_inner';

        $options = $this->User->find('first', array('conditions' => array('User.id' => $id),'recursive'=>-1));
        $this->set('userAbout', $options);
        
        $this->loadModel('Support');
        $supporting = $this->Support->find('all',array(
            'conditions'=>array(
                'Support.user_id'=>$id
            ),
            'fields'=>array('Support.touserid')
        ));
        $supporting_user=array();
        /*---check if users profile block by logged user---*/


        $is_block = $this->Support->find('first',array(
            'conditions'=>array(
                'Support.user_id'=>$id,
                'Support.touserid'=>$this->Auth->user('id')
                //'Support.block'=>1
            ),
            'fields'=>array('Support.block'),
            'order'=>array('Support.id'=>'desc')
        ));
 
        if( $is_block['Support']['block'] == 1){
            $this->set('block',1);
        }

        /*---check if users profile block by logged user---*/
        foreach($supporting as $support){
            array_push($supporting_user, $support['Support']['touserid']);
        }
//        debug($supporting_user);

        $exist = in_array($this->Auth->user('id'),$supporting_user);
        $profile_public = 0;
        if(empty($exist)){
            if($options['User']['profile_status'] == 1){
                $profile_public = 1;
            }
        }
        
        $this->set('profile_public', $profile_public);
        
        $this->loadModel('Online');
        $onlinesmemberf = $this->Online->find('count', array('conditions' => array('Online.user_id' => $id)));
        $this->set('onlinesmemberf', $onlinesmemberf);


        $this->loadModel('Post');
        $dataImage = $this->Post->find('all', array(
            'recursive' => -1,
            'limit' => 4,
            'conditions' => array('AND' => array('Post.user_id' => $id), 'OR' => array('Post.type' => array('image')))));
        $this->set('pdata', $dataImage);

        $dataVideo = $this->Post->find('all', array(
            'recursive' => -1,
            'limit'=>4,
            'conditions' => array('AND' => array('Post.user_id' => $id), 'OR' => array(
                    'Post.type' => array('video')
        ))));
        $this->set('videoData', $dataVideo);


       

        // load posts starts here
        $this->loadModel('Post');
        $items_per_group = 1;
        //$cont = $this->Post->find('count');
        $cont = $this->Post->find('count',array('conditions'=>array('Post.user_id'=>$id)));
        $total_groups = (int) ceil($cont / $items_per_group);
//         debug($total_groups);
        $this->set(compact('total_groups'));
        $this->set("current_userid", $id);
        // load posts ends here

        
    }

    public function usersupport($id = NULL) {
        configure::write("debug", 2);
        $this->layout = 'ajax';
        $this->loadModel('Message');
        //debug($this->request->data);exit;
        $id = $this->User->find('first', array('conditions' => array('User.id' => $id)));
        if ($this->request->is('post')) {
            $this->request->data['Message']['created'] = date('F d, Y h:i A');
            $this->request->data['Message']['user_id'] = $this->Auth->User('id');
            $this->request->data['Message']['status'] = 1;
            $this->Message->create();
            if ($this->Message->save($this->request->data)) {
                $this->set("res", array('r' => 1));
                $this->response->type('json');
                $this->render('/Common/ajax', 'ajax');
            }
        }
    }

    public function usersupporta() {

        configure::write("debug", 2);
//          debug($this->request->data);
//          exit;        

        $this->loadModel('Message');
        $id = $this->request->data['Message']['friend_id'];
        $this->request->data['Message']['message'] = $this->request->data['User']['message'];
        if ($this->request->is('post')) {
            $this->request->data['Message']['created'] = date('F d, Y h:i A');
            $this->request->data['Message']['user_id'] = $this->Auth->User('id');
            $this->request->data['Message']['status'] = 1;
            $this->Message->create();
            if ($this->Message->save($this->request->data)) {
                return $this->redirect(array('controller' => 'users', 'action' => 'userview/' . $id . ''));
            }
        }
    }

    public function colorchange() {
        $this->layout = 'ajax';
        $id = $this->Auth->User('id');
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid User'));
        }
        if ($this->request->is(array('post'))) {
            debug($this->request->data);
            $color = $this->request->data['color'];
            //exit;
            $this->User->id = $id;
            // $this->User->saveField('color', true);
            if ($this->User->saveField('color', $color)) {
                $response['msg'] = "color changed";
            } else {
                $response['msg'] = "not change";
            }
        }
        $this->set('response', $response);
        $this->render('/Common/ajax');
    }

    public function workplaces() {
        $this->layout = 'ajax';
        if ($this->request->is('post')) {
            $this->User->create();
            $data = $this->request->data;
            if ($this->User->save($this->request->data)) {
                $response['data'] = $data;
                $response['success'] = "success";
            } else {
                $response['success'] = "failed";
            }
            echo json_encode($response);
            $this->render('workplace');
        }
    }

    public function profilestatus() {
        $this->layout = "ajax";
        if ($this->User->save($this->request->data)) {
            $response['error'] = 0;
            $response['id'] = '#pr_' . $this->request->data['User']['id'] . "_" . $this->request->data['User']['profile_status'];
            if($this->request->data['User']['profile_status'] == 1){
                 $response['msg'] = 'Now users who you are NOT supporting will not be able to see your Posts, Pictures, or Videos';
            }else{
                 $response['msg'] = 'Now users who you are not supporting will  also be able to see your posts, pictures, and videos';
            }
        } else {
            $response['error'] = 1;
            $response['msg'] = 'Status updated failed.Please Try Again';
        }
        $this->set('response', $response);
        $this->render('ajax3');
    }

    public function messagestatus() {
        $this->layout = "ajax";
        if ($this->User->save($this->request->data)) {
            $response['error'] = 0;
            $response['id'] = '#mstatus_' . $this->request->data['User']['id'] . "_" . $this->request->data['User']['messageid'];
            $response['msg'] = 'Status updated';
        } else {
            $response['error'] = 1;
            $response['msg'] = 'Status updated failed';
        }
        $this->set('response', $response);
        $this->render('ajax3');
    }
    
    public function soundnotifications() {
        $this->layout = "ajax";
        $this->request->data['User']['id']=$this->Auth->user('id');
        if ($this->User->save($this->request->data)) {
            $response['error'] = 0;
            $response['id'] = '#sound_' . $this->request->data['User']['id'] . "_" . $this->request->data['User']['sound_notifications'];
            $response['msg'] = 'Status updated';
        } else {
            $response['error'] = 1;
            $response['msg'] = 'Status updated failed';
        }
        $this->set('response', $response);
        $this->render('ajax3');
    }

    public function poststatus() {
        $this->layout = "ajax";
        if ($this->User->save($this->request->data)) {
            $response['error'] = 0;
            $response['id'] = '#rolep_' . $this->request->data['User']['id'] . "_" . $this->request->data['User']['pciv_status'];
            $response['msg'] = 'Status updated';
        } else {
            $response['error'] = 1;
            $response['msg'] = 'Status updated failed';
        }
        $this->set('response', $response);
        $this->render('ajax3');
    }

    public function supportstatus() {
        $this->layout = "ajax";
        if ($this->User->save($this->request->data)) {
            $response['error'] = 0;
            $response['id'] = '#sprt_' . $this->request->data['User']['id'] . "_" . $this->request->data['User']['support_ststus'];
            $response['msg'] = 'Status updated';
        } else {
            $response['error'] = 1;
            $response['msg'] = 'Status updated failed';
        }
        $this->set('response', $response);
        $this->render('ajax3');
    }

    public function autosearchtop() {
        $this->layout = "ajax";
        configure::write('debug', 0);
        
        $keyword = $this->request->data['keyword'];
        /*
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
        ))));
        */
        
       /* $obj1 = $this->User->find('all', array('conditions' => array('OR' => array(
                'concat_ws(" ", User.firstname, User.lastname) LIKE ' => '%'. $keyword . '%',
                'User.state LIKE' => '%' . $keyword . '%',
                'User.city LIKE' => '%' . $keyword . '%'
        ))));*/
          $obj1 = $this->User->find('all', array('contain'=>false,'fields'=>array('id','firstname','lastname','image'),'conditions' => array(

                'AND'=>array(
                       'OR' => array('User.firstname LIKE ' => '%'. $keyword . '%',
                         'User.state LIKE' => '%' . $keyword . '%',
                         'User.city LIKE' => '%' . $keyword . '%'
                    ),
                 'User.email !=' => GUEST
                )
        )));
        $this->set('res', $obj1);
        $this->render('../Common/ajax');
    }
    
    public function autosearchpost() {
        $this->layout = "ajax";
        configure::write('debug', 0);
        
        $keyword = $this->request->data['keyword'];
        
        $this->loadModel('Support');
        $supports = $this->Support->find('all',array('conditions'=>array(
                'Support.touserid'=>$this->Auth->User('id')
        )));
        
        $support_id = array();
        foreach($supports as $support){
            array_push($support_id,$support['Support']['user_id']);
        }
//        debug($support_id);
        /*
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
        ))));
        */
        $obj1 = $this->User->find('all', array('conditions' => array(
            'And'=>array(
                'User.id'=>$support_id,
                ' concat_ws(" ", User.firstname, User.lastname) LIKE ' => $keyword . '%'
            )
        )));
        $obj2 = $this->User->find('all', array('conditions' => array('OR' => array(
                'concat_ws(" ", User.firstname, User.lastname) LIKE ' => $keyword . '%',
                'User.state LIKE' => '%' . $keyword . '%',
                'User.city LIKE' => '%' . $keyword . '%'
        ))));

        $this->set('res', $obj1);
        $this->render('../Common/ajax');
    }
    public function ajaxCall() {
        $this->layout = "ajax";
        configure::write('debug', 0);
        $this->loadModel('Location');
        $location_id = $_POST['location_id'];
        $locationType = $_POST['location_type'];
        //$types = array('country', 'State', 'City');
        /* $obj = $this->Location->find('all',array(
          'conditions' => array(
          'AND' => array(
          'Location.location_type' => $locationType,
          'Location.parent_id' => $location_id
          )
          )
          )); */
        $qry = "select * from `locations` as Location where `location_type`=$locationType and `parent_id` = $location_id ORDER BY `name` ASC";
        $obj = $this->Location->query($qry);
        // debug($obj);exit;
        $this->set('obj', $obj);
        $this->render('ajax1');
    }
    
        public function status_active() {

        $this->layout = 'ajax';

        $this->User->id = $this->request->data['User']['id'];
        

        if ($this->User->exists()) {

            $this->User->saveField("status",1);

            $response['status'] = 1;
        } else {

            $response['status'] = 0;
        }

        $this->set('res', $response);

       $this->response->type('json');

            $this->render("/Common/ajax", "ajax");
    }
   

    public function img_save_to_file(){
        $this->layout = 'ajax';
        if ($this->request->is('post')) {
            $imagePath = WWW_ROOT."/files/coverphoto/original/";

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
                    "status" => 'error',
                    "message" => 'Can`t upload File; no write Access'
                );
               // print json_encode($response);
               // return;
            }
	
            if ( in_array($extension, $allowedExts)){
                if ($_FILES["img"]["error"] > 0){
                    $response = array(
                           "status" => 'error',
                           "message" => 'ERROR Return Code: '. $_FILES["img"]["error"],
                   );			
                }else{
                    $filename = $_FILES["img"]["tmp_name"];
                    list($width, $height) = getimagesize( $filename );
                    $image_name= $current_date.$image;
                    if(move_uploaded_file($filename,  $imagePath . $image_name)){
                        $this->loadModel('Gallery');
                        $this->request->data['Gallery']['image']=$image_name;
                        $this->request->data['Gallery']['user_id']=$this->Auth->user('id');
                        $this->request->data['Gallery']['type']="landscape";
                        $this->Gallery->create();
                        if($this->Gallery->save($this->request->data)){
                            $gallery="Data saved into gallery"; 
                        }else{
                            $gallery="Data did/'t saved into gallery";
                        }
                    }
                    
                    /*$imageUrl="https://" . $_SERVER['SERVER_NAME']. "/files/coverphoto/original/";*/
                    $imageUrl=host. "/files/coverphoto/original/";
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
             $response = array(
                    "status" => 'error',
                    "message" => 'something went wrong, most likely file is to large for upload. check upload_max_filesize, post_max_size and memory_limit in you php.ini',
		);
            }
	  
            //print json_encode($response);
            $this->set('res', $response);

            $this->response->type('json');

            $this->render("/Common/ajax", "ajax");
        }
        
    }
    public function img_crop_to_file(){
       
       Configure::write('debug',0);
        $this->layout = 'ajax';
        if($this->request->is('post')){
             
            $imgUrl = $_POST['imgUrl']; //path to image example "http://mysponsers.com/temp/old iphone5 017.JPG"

            $exploded = explode("/",$imgUrl);
            $count = count($exploded);
            $image_name = $exploded[$count-1]; // image name eg. old iphone5 017.JPG
            
            // original sizes
            $imgInitW = $_POST['imgInitW'];
            $imgInitH = $_POST['imgInitH'] ;
            // resized sizes
            $imgW = $_POST['imgW'];
            $imgH = $_POST['imgH'];
            // offsets
            $imgY1 = $_POST['imgY1'];
            $imgX1 = $_POST['imgX1'];
            // crop box
            $cropW = $_POST['cropW'];
            $cropH = $_POST['cropH'];// == 0 ? ( int ) ( $_POST['cropW'] /2 ) : $_POST['cropH'];;
            // rotation angle
            $angle = $_POST['rotation'];

            $jpeg_quality = 100;
//            $random = rand();
            $current_date =date("Ymdhis");
            //$output_filename = "http://" . $_SERVER['SERVER_NAME']. "/temp/croppedImg_".rand();
            $output_filename = WWW_ROOT."/files/coverphoto/croppedImg_".$current_date;
            $output_url = host. "/files/coverphoto/croppedImg_".$current_date;
            // uncomment line below to save the cropped image in the same location as the original image.
            //$output_filename = dirname($imgUrl). "/croppedImg_".rand();

            $what = getimagesize(host.'/files/coverphoto/original/'.$image_name);

              $imgUrl = host.'/files/coverphoto/original/'.$image_name;


            switch(strtolower($what['mime']))
            {
                case 'image/png':
                    $img_r = imagecreatefrompng($imgUrl);
                            $source_image = imagecreatefrompng($imgUrl);
                            $type = '.png';
                    break;
                case 'image/jpeg':
                    $img_r = imagecreatefromjpeg($imgUrl);
                            $source_image = imagecreatefromjpeg($imgUrl);
                            error_log("jpg");
                            $type = '.jpeg';
                    break;
                case 'image/gif':
                    $img_r = imagecreatefromgif($imgUrl);
                            $source_image = imagecreatefromgif($imgUrl);
                            $type = '.gif';
                    break;
                default: die("image type not supportedd");
            }

             /*----------------------------------start by vipin-----------------*/




  /*$image = imagecreatefromstring(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/app/webroot/files/coverphoto/original/'.$image_name));*/
$exif = exif_read_data(WWW_ROOT.'/files/coverphoto/original/'.$image_name);
if(!empty($exif['Orientation'])) {
    switch($exif['Orientation']) {
        case 8:
           // $source_image = imagerotate($image,90,0);
          $angle = 90;
            break;
        case 3:
           //$source_image = imagerotate($image,180,0);
        $angle = 180;
            break;
        case 6:
            //$source_image = imagerotate($image,-90,0);
        $angle = 90;
            break;
    }
}




          /* if (isset($exif['Orientation']))
                    {
                        switch ($exif['Orientation'])
                        {
                            case 3:
                                // Need to rotate 180 deg
                                $source_image = imagecreatefromjpeg($imgUrl);
                                $rotate = imagerotate($source_image, -180, 0);
                                imagejpeg($rotate, $imgUrl);
                                break;

                            case 6:
                                // Need to rotate 90 deg clockwise
                                $source_image = imagecreatefromjpeg($imgUrl);
                                $rotate = imagerotate( $source_image, -90, 0);
                                imagejpeg($rotate, $imgUrl);
                                break;

                            case 8:
                                // Need to rotate 90 deg counter clockwise
                                $source_image = imagecreatefromjpeg($imgUrl);
                                $rotate = imagerotate($source_image, 90, 0);
                                imagejpeg($rotate, $imgUrl);
                                break;
                        }
                    }*/
          /*----------------------------------End by vipin--------------------*/


//Check write Access to Directory

            if(!is_writable(dirname($output_filename))){
                    $response = Array(
                        "status" => 'error',
                        "message" => 'Can`t write cropped File'
                );	
            }else{
                $img_db= "croppedImg_".$current_date.$type;
                // resize the original image to size of editor
                $resizedImage = imagecreatetruecolor($imgW, $imgH);
                    imagecopyresampled($resizedImage, $source_image, 0, 0, 0, 0, $imgW, $imgH, $imgInitW, $imgInitH);
                // rotate the rezized image
                $rotated_image = imagerotate($resizedImage, -$angle, 0);
                // find new width & height of rotated image
                $rotated_width = imagesx($rotated_image);
                $rotated_height = imagesy($rotated_image);
                // diff between rotated & original sizes
                $dx = $rotated_width - $imgW;
                $dy = $rotated_height - $imgH;
                // crop rotated image to fit into original rezized rectangle
                $cropped_rotated_image = imagecreatetruecolor($imgW, $imgH);
                imagecolortransparent($cropped_rotated_image, imagecolorallocate($cropped_rotated_image, 0, 0, 0));
                imagecopyresampled($cropped_rotated_image, $rotated_image, 0, 0, $dx / 2, $dy / 2, $imgW, $imgH, $imgW, $imgH);
                // crop image into selected area
                $final_image = imagecreatetruecolor($cropW, $cropH);
                imagecolortransparent($final_image, imagecolorallocate($final_image, 0, 0, 0));
                imagecopyresampled($final_image, $cropped_rotated_image, 0, 0, $imgX1, $imgY1, $cropW, $cropH, $cropW, $cropH);
                // finally output png image
                //imagepng($final_image, $output_filename.$type, $png_quality);
                imagejpeg($final_image, $output_filename.$type, $jpeg_quality);
                $this->User->id=$this->Auth->user('id');
                $this->User->saveField("coverphoto",$img_db);
                
                $response = Array(
                    "status" => 'success',
                    "url" => $output_url.$type
                );
            }
//print json_encode($response);
            $this->set('res', $response);

            $this->response->type('json');

            $this->render("/Common/ajax", "ajax");
        }  
    }
/*
 * save original profile image 
 */    
    public function profile_img_save_to_file(){
        $this->layout = 'ajax';
        if ($this->request->is('post')) {
            /*$imagePath = $_SERVER['DOCUMENT_ROOT']."/app/webroot/files/profile/original/";*/

            $imagePath = WWW_ROOT . 'files/profile/original/';

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
                    "status" => 'error',
                    "message" => 'Can`t upload File; no write Access'
                );
               // print json_encode($response);
               // return;
            }
	
            if ( in_array($extension, $allowedExts)){
                if ($_FILES["img"]["error"] > 0){
                    $response = array(
                           "status" => 'error',
                           "message" => 'ERROR Return Code: '. $_FILES["img"]["error"],
                   );			
                }else{
                    $filename = $_FILES["img"]["tmp_name"];
                    list($width, $height) = getimagesize( $filename );
                    $image_name= $current_date.$image;
                    if(move_uploaded_file($filename,  $imagePath . $image_name)){

                        $this->User->id=$this->Auth->user('id');
                        $this->User->saveField("original_image",$image_name);
                        
                        $this->loadModel('Gallery');
                        $this->request->data['Gallery']['image']=$image_name;
                        $this->request->data['Gallery']['user_id']=$this->Auth->user('id');
                        $this->request->data['Gallery']['type']="profile";
                        $this->Gallery->create();
                        if($this->Gallery->save($this->request->data)){
                            $gallery="Data saved into gallery"; 
                        }else{
                            $gallery="Data did/'t saved into gallery";
                        }
                        
                    }
                    /*$imageUrl="https://" . $_SERVER['SERVER_NAME']. "/files/profile/original/";*/
                    $imageUrl=host. "/files/profile/original/";

                  

                    

                    $response = array(
                          "status" => 'success',
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
                    "status" => 'error',
                    "message" => 'something went wrong, most likely file is to large for upload. check upload_max_filesize, post_max_size and memory_limit in you php.ini',
		);
            }
	  
            //print json_encode($response);
            $this->set('res', $response);

            $this->response->type('json');

            $this->render("/Common/ajax", "ajax");
        }
        
    }
    //cropp and save profile image
    public function profile_img_crop_to_file(){
        Configure::write('debug',0);
        $this->layout = 'ajax';
        if($this->request->is('post')){
            $imgUrl = $_POST['imgUrl']; //path to image example "http://mysponsers.com/temp/old iphone5 017.JPG"

            $exploded = explode("/",$imgUrl);
            $count = count($exploded);
            $image_name = $exploded[$count-1]; // image name eg. old iphone5 017.JPG
            
            // original sizes
            $imgInitW = $_POST['imgInitW'];
            $imgInitH = $_POST['imgInitH'];
            // resized sizes
            $imgW = $_POST['imgW'];
            $imgH = $_POST['imgH'];
            // offsets
            $imgY1 = $_POST['imgY1'];
            $imgX1 = $_POST['imgX1'];
            // crop box
            $cropW = $_POST['cropW'];
            $cropH = $_POST['cropH'];
            // rotation angle
            $angle = $_POST['rotation'];

            $jpeg_quality = 100;
//            $random = rand();
            $current_date =date("Ymdhis");
            //$output_filename = "http://" . $_SERVER['SERVER_NAME']. "/temp/croppedImg_".rand();
            $output_filename = WWW_ROOT."/files/profile/croppedImg_".$current_date;
            $output_url = host. "/files/profile/croppedImg_".$current_date;
            // uncomment line below to save the cropped image in the same location as the original image.
            //$output_filename = dirname($imgUrl). "/croppedImg_".rand();

            $what = getimagesize(WWW_ROOT.'/files/profile/original/'.$image_name);
              
 

            $imgUrl = WWW_ROOT.'/files/profile/original/'.$image_name;


            switch(strtolower($what['mime']))
            {
                case 'image/png':
                    $img_r = imagecreatefrompng($imgUrl);
                            $source_image = imagecreatefrompng($imgUrl);
                            $type = '.png';
                    break;
                case 'image/jpeg':
                    $img_r = imagecreatefromjpeg($imgUrl);
                            $source_image = imagecreatefromjpeg($imgUrl);
                            error_log("jpg");
                            $type = '.jpeg';
                    break;
                case 'image/gif':
                    $img_r = imagecreatefromgif($imgUrl);
                            $source_image = imagecreatefromgif($imgUrl);
                            $type = '.gif';
                    break;
                default: die("image type not supportedd");
            }






              /*----------------------------------start by vipin-----------------*/




/*  $image = imagecreatefromstring(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/mysponsers/app/webroot/files/profile/original/'.$image_name));*/

$exif = exif_read_data(WWW_ROOT.'/files/profile/original/'.$image_name);

if(!empty($exif['Orientation'])) {
    switch($exif['Orientation']) {
        case 8:
           // $source_image = imagerotate($image,90,0);
          $angle = 90;
            break;
        case 3:
           //$source_image = imagerotate($image,180,0);
        $angle = 180;
            break;
        case 6:
            //$source_image = imagerotate($image,-90,0);
        $angle = 90;
            break;
    }
}




          /*----------------------------------End by vipin--------------------*/



//Check write Access to Directory

            if(!is_writable(dirname($output_filename))){
                    $response = Array(
                        "status" => 'error',
                        "message" => 'Can`t write cropped File'
                );	
            }else{
                $img_db= "croppedImg_".$current_date.$type;
                // resize the original image to size of editor
                $resizedImage = imagecreatetruecolor($imgW, $imgH);
                    imagecopyresampled($resizedImage, $source_image, 0, 0, 0, 0, $imgW, $imgH, $imgInitW, $imgInitH);
                // rotate the rezized image
                $rotated_image = imagerotate($resizedImage, -$angle, 0);
                // find new width & height of rotated image
                $rotated_width = imagesx($rotated_image);
                $rotated_height = imagesy($rotated_image);
                // diff between rotated & original sizes
                $dx = $rotated_width - $imgW;
                $dy = $rotated_height - $imgH;
                // crop rotated image to fit into original rezized rectangle
                $cropped_rotated_image = imagecreatetruecolor($imgW, $imgH);
                imagecolortransparent($cropped_rotated_image, imagecolorallocate($cropped_rotated_image, 0, 0, 0));
                imagecopyresampled($cropped_rotated_image, $rotated_image, 0, 0, $dx / 2, $dy / 2, $imgW, $imgH, $imgW, $imgH);
                // crop image into selected area
                $final_image = imagecreatetruecolor($cropW, $cropH);
                imagecolortransparent($final_image, imagecolorallocate($final_image, 0, 0, 0));
                imagecopyresampled($final_image, $cropped_rotated_image, 0, 0, $imgX1, $imgY1, $cropW, $cropH, $cropW, $cropH);
                // finally output png image
                //imagepng($final_image, $output_filename.$type, $png_quality);
                imagejpeg($final_image, $output_filename.$type, $jpeg_quality);
                $this->User->id=$this->Auth->user('id');
                $this->User->saveField("image",$img_db);
                
                $response = Array(
                    "status" => 'success',
                    "url" => $output_url.$type
                );
            }
//print json_encode($response);
            $this->set('res', $response);

            $this->response->type('json');

            $this->render("/Common/ajax", "ajax");
        }  
    }
    public function profile_signup_img_save_to_file($id=NULL){
        $this->layout = 'ajax';
        if ($this->request->is('post')) {
            $imagePath = WWW_ROOT."/files/profile/original/";

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
                    "status" => 'error',
                    "message" => 'Can`t upload File; no write Access'
                );
               // print json_encode($response);
               // return;
            }
	
            if ( in_array($extension, $allowedExts)){
                if ($_FILES["img"]["error"] > 0){
                    $response = array(
                           "status" => 'error',
                           "message" => 'ERROR Return Code: '. $_FILES["img"]["error"],
                   );			
                }else{
                    $filename = $_FILES["img"]["tmp_name"];
                    list($width, $height) = getimagesize( $filename );
                    $image_name= $current_date.$image;
                    if(move_uploaded_file($filename,  $imagePath . $image_name)){
                        if($id){
                            $this->User->id=$id; // before login
                        }else{
                            $this->User->id=$this->Auth->user('id'); //after login
                        }
                        
                        $this->User->saveField("original_image",$image_name);
                        
                        $this->loadModel('Gallery');
                        $this->request->data['Gallery']['image']=$image_name;
                        $this->request->data['Gallery']['user_id']=$id;
                        $this->request->data['Gallery']['type']="profile";
                        $this->Gallery->create();
                        if($this->Gallery->save($this->request->data)){
                            $gallery="Data saved into gallery"; 
                        }else{
                            $gallery="Data did/'t saved into gallery";
                        }
                        
                    }
                    /*$imageUrl="https://" . $_SERVER['SERVER_NAME']. "/files/profile/original/";*/
                    $imageUrl="https://" . $_SERVER['SERVER_NAME']. "/files/profile/original/";
                    $response = array(
                          "status" => 'success',
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
                    "status" => 'error',
                    "message" => 'something went wrong, most likely file is to large for upload. check upload_max_filesize, post_max_size and memory_limit in you php.ini',
		);
            }
	  
            //print json_encode($response);
            $this->set('res', $response);

            $this->response->type('json');

            $this->render("/Common/ajax", "ajax");
        }
        
    }
    //cropp and save profile image
    public function profile_signup_img_crop_to_file($id=null){
        Configure::write('debug',0);
        $this->layout = 'ajax';
        if($this->request->is('post')){
            $imgUrl = $_POST['imgUrl']; //path to image example "http://mysponsers.com/temp/old iphone5 017.JPG"

            $exploded = explode("/",$imgUrl);
            $count = count($exploded);
            $image_name = $exploded[$count-1]; // image name eg. old iphone5 017.JPG
            
            // original sizes
            $imgInitW = $_POST['imgInitW'];
            $imgInitH = $_POST['imgInitH'];
            // resized sizes
            $imgW = $_POST['imgW'];
            $imgH = $_POST['imgH'];
            // offsets
            $imgY1 = $_POST['imgY1'];
            $imgX1 = $_POST['imgX1'];
            // crop box
            $cropW = $_POST['cropW'];
            $cropH = $_POST['cropH'] == 0 ? $_POST['cropW']: $_POST['cropH'];
            // rotation angle
            $angle = $_POST['rotation'];

            $jpeg_quality = 100;
//            $random = rand();
            $current_date =date("Ymdhis");
            //$output_filename = "http://" . $_SERVER['SERVER_NAME']. "/temp/croppedImg_".rand();
            $output_filename = $_SERVER['DOCUMENT_ROOT']."/app/webroot/files/profile/croppedImg_".$current_date;
            $output_url = "https://" . $_SERVER['SERVER_NAME']. "/files/profile/croppedImg_".$current_date;
            // uncomment line below to save the cropped image in the same location as the original image.
            //$output_filename = dirname($imgUrl). "/croppedImg_".rand();

            //$what = getimagesize($imgUrl);

            $what = getimagesize($_SERVER['DOCUMENT_ROOT'].'/app/webroot/files/profile/original/'.$image_name);
              
 

            $imgUrl = $_SERVER['DOCUMENT_ROOT'].'/app/webroot/files/profile/original/'.$image_name;


            switch(strtolower($what['mime']))
            {
                case 'image/png':
                    $img_r = imagecreatefrompng($imgUrl);
                            $source_image = imagecreatefrompng($imgUrl);
                            $type = '.png';
                    break;
                case 'image/jpeg':
                    $img_r = imagecreatefromjpeg($imgUrl);
                            $source_image = imagecreatefromjpeg($imgUrl);
                            error_log("jpg");
                            $type = '.jpeg';
                    break;
                case 'image/gif':
                    $img_r = imagecreatefromgif($imgUrl);
                            $source_image = imagecreatefromgif($imgUrl);
                            $type = '.gif';
                    break;
                default: die("image type not supportedd");
            }


             /*----------------------------------start by vipin-----------------*/




  $image = imagecreatefromstring(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/app/webroot/files/profile/original/'.$image_name));
$exif = exif_read_data($_SERVER['DOCUMENT_ROOT'].'/app/webroot/files/profile/original/'.$image_name);
if(!empty($exif['Orientation'])) {
    switch($exif['Orientation']) {
        case 8:
           // $source_image = imagerotate($image,90,0);
          $angle = 90;
            break;
        case 3:
           //$source_image = imagerotate($image,180,0);
        $angle = 180;
            break;
        case 6:
            //$source_image = imagerotate($image,-90,0);
        $angle = 90;
            break;
    }
}




          /* if (isset($exif['Orientation']))
                    {
                        switch ($exif['Orientation'])
                        {
                            case 3:
                                // Need to rotate 180 deg
                                $source_image = imagecreatefromjpeg($imgUrl);
                                $rotate = imagerotate($source_image, -180, 0);
                                imagejpeg($rotate, $imgUrl);
                                break;

                            case 6:
                                // Need to rotate 90 deg clockwise
                                $source_image = imagecreatefromjpeg($imgUrl);
                                $rotate = imagerotate( $source_image, -90, 0);
                                imagejpeg($rotate, $imgUrl);
                                break;

                            case 8:
                                // Need to rotate 90 deg counter clockwise
                                $source_image = imagecreatefromjpeg($imgUrl);
                                $rotate = imagerotate($source_image, 90, 0);
                                imagejpeg($rotate, $imgUrl);
                                break;
                        }
                    }*/
          /*----------------------------------End by vipin--------------------*/


//Check write Access to Directory

            if(!is_writable(dirname($output_filename))){
                    $response = Array(
                        "status" => 'error',
                        "message" => 'Can`t write cropped File'
                );	
            }else{
                $img_db= "croppedImg_".$current_date.$type;
                // resize the original image to size of editor
                $resizedImage = imagecreatetruecolor($imgW, $imgH);
                    imagecopyresampled($resizedImage, $source_image, 0, 0, 0, 0, $imgW, $imgH, $imgInitW, $imgInitH);
                // rotate the rezized image
                $rotated_image = imagerotate($resizedImage, -$angle, 0);
                // find new width & height of rotated image
                $rotated_width = imagesx($rotated_image);
                $rotated_height = imagesy($rotated_image);
                // diff between rotated & original sizes
                $dx = $rotated_width - $imgW;
                $dy = $rotated_height - $imgH;
                // crop rotated image to fit into original rezized rectangle
                $cropped_rotated_image = imagecreatetruecolor($imgW, $imgH);
                imagecolortransparent($cropped_rotated_image, imagecolorallocate($cropped_rotated_image, 0, 0, 0));
                imagecopyresampled($cropped_rotated_image, $rotated_image, 0, 0, $dx / 2, $dy / 2, $imgW, $imgH, $imgW, $imgH);
                // crop image into selected area
                $final_image = imagecreatetruecolor($cropW, $cropH);
                imagecolortransparent($final_image, imagecolorallocate($final_image, 0, 0, 0));
                imagecopyresampled($final_image, $cropped_rotated_image, 0, 0, $imgX1, $imgY1, $cropW, $cropH, $cropW, $cropH);
                // finally output png image
                //imagepng($final_image, $output_filename.$type, $png_quality);
                imagejpeg($final_image, $output_filename.$type, $jpeg_quality);
                if($id){
                    $this->User->id=$id; //before login
                }else{
                    $this->User->id=$this->Auth->user('id'); //after login
                }
                
                $this->User->saveField("image",$img_db);
                
                $response = Array(
                    "status" => 'success',
                    "url" => $output_url.$type
                );
            }
//print json_encode($response);
            $this->set('res', $response);

            $this->response->type('json');

            $this->render("/Common/ajax", "ajax");
        }  
    }
    
    // signup cover pic
    
        public function signup_img_save_to_file($id=NULL){
        $this->layout = 'ajax';
        if ($this->request->is('post')) {
            $imagePath = $_SERVER['DOCUMENT_ROOT']."/app/webroot/files/coverphoto/original/";

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
                    "status" => 'error',
                    "message" => 'Can`t upload File; no write Access'
                );
               // print json_encode($response);
               // return;
            }
	
            if ( in_array($extension, $allowedExts)){
                if ($_FILES["img"]["error"] > 0){
                    $response = array(
                           "status" => 'error',
                           "message" => 'ERROR Return Code: '. $_FILES["img"]["error"],
                   );			
                }else{
                    $filename = $_FILES["img"]["tmp_name"];
                    list($width, $height) = getimagesize( $filename );
                    $image_name= $current_date.$image;
                    if(move_uploaded_file($filename,  $imagePath . $image_name)){
                        $this->loadModel('Gallery');
                        $this->request->data['Gallery']['image']=$image_name;
                        $this->request->data['Gallery']['user_id']=$id;
                        $this->request->data['Gallery']['type']="landscape";
                        $this->Gallery->create();
                        if($this->Gallery->save($this->request->data)){
                            $gallery="Data saved into gallery"; 
                        }else{
                            $gallery="Data did/'t saved into gallery";
                        }
                    }
                    
                    /*$imageUrl="https://" . $_SERVER['SERVER_NAME']. "/files/coverphoto/original/";*/
                    $imageUrl="http://" . $_SERVER['SERVER_NAME']. "/files/coverphoto/original/";
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
             $response = array(
                    "status" => 'error',
                    "message" => 'something went wrong, most likely file is to large for upload. check upload_max_filesize, post_max_size and memory_limit in you php.ini',
		);
            }
	  
            //print json_encode($response);
            $this->set('res', $response);

            $this->response->type('json');

            $this->render("/Common/ajax", "ajax");
        }
        
    }
    public function signup_img_crop_to_file($id=NULL){
        Configure::write('debug',0);
        $this->layout = 'ajax';
        if($this->request->is('post')){
            $imgUrl = $_POST['imgUrl']; //path to image example "http://mysponsers.com/temp/old iphone5 017.JPG"

            $exploded = explode("/",$imgUrl);
            $count = count($exploded);
            $image_name = $exploded[$count-1]; // image name eg. old iphone5 017.JPG
            
            // original sizes
            $imgInitW = $_POST['imgInitW'];
            $imgInitH = $_POST['imgInitH'];
            // resized sizes
            $imgW = $_POST['imgW'];
            $imgH = $_POST['imgH'];
            // offsets
            $imgY1 = $_POST['imgY1'];
            $imgX1 = $_POST['imgX1'];
            // crop box
            $cropW = $_POST['cropW'];
            $cropH = $_POST['cropH'] == 0 ? ( int ) ($_POST['cropW']/2):$_POST['cropH'];
            // rotation angle
            $angle = $_POST['rotation'];

            $jpeg_quality = 100;
//            $random = rand();
            $current_date =date("Ymdhis");
            //$output_filename = "http://" . $_SERVER['SERVER_NAME']. "/temp/croppedImg_".rand();
            $output_filename = $_SERVER['DOCUMENT_ROOT']."/app/webroot/files/coverphoto/croppedImg_".$current_date;
            $output_url = "https://" . $_SERVER['SERVER_NAME']. "/files/coverphoto/croppedImg_".$current_date;
            // uncomment line below to save the cropped image in the same location as the original image.
            //$output_filename = dirname($imgUrl). "/croppedImg_".rand();

            //$what = getimagesize($imgUrl);
 $what = getimagesize($_SERVER['DOCUMENT_ROOT'].'/app/webroot/files/coverphoto/original/'.$image_name);

              $imgUrl = $_SERVER['DOCUMENT_ROOT'].'/app/webroot/files/coverphoto/original/'.$image_name;

            switch(strtolower($what['mime']))
            {
                case 'image/png':
                    $img_r = imagecreatefrompng($imgUrl);
                            $source_image = imagecreatefrompng($imgUrl);
                            $type = '.png';
                    break;
                case 'image/jpeg':
                    $img_r = imagecreatefromjpeg($imgUrl);
                            $source_image = imagecreatefromjpeg($imgUrl);
                            error_log("jpg");
                            $type = '.jpeg';
                    break;
                case 'image/gif':
                    $img_r = imagecreatefromgif($imgUrl);
                            $source_image = imagecreatefromgif($imgUrl);
                            $type = '.gif';
                    break;
                default: die("image type not supportedd");
            }


              /*----------------------------------start by vipin-----------------*/




  $image = imagecreatefromstring(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/app/webroot/files/coverphoto/original/'.$image_name));
$exif = exif_read_data($_SERVER['DOCUMENT_ROOT'].'/app/webroot/files/coverphoto/original/'.$image_name);
if(!empty($exif['Orientation'])) {
    switch($exif['Orientation']) {
        case 8:
           // $source_image = imagerotate($image,90,0);
          $angle = 90;
            break;
        case 3:
           //$source_image = imagerotate($image,180,0);
        $angle = 180;
            break;
        case 6:
            //$source_image = imagerotate($image,-90,0);
        $angle = 90;
            break;
    }
}




          /* if (isset($exif['Orientation']))
                    {
                        switch ($exif['Orientation'])
                        {
                            case 3:
                                // Need to rotate 180 deg
                                $source_image = imagecreatefromjpeg($imgUrl);
                                $rotate = imagerotate($source_image, -180, 0);
                                imagejpeg($rotate, $imgUrl);
                                break;

                            case 6:
                                // Need to rotate 90 deg clockwise
                                $source_image = imagecreatefromjpeg($imgUrl);
                                $rotate = imagerotate( $source_image, -90, 0);
                                imagejpeg($rotate, $imgUrl);
                                break;

                            case 8:
                                // Need to rotate 90 deg counter clockwise
                                $source_image = imagecreatefromjpeg($imgUrl);
                                $rotate = imagerotate($source_image, 90, 0);
                                imagejpeg($rotate, $imgUrl);
                                break;
                        }
                    }*/
          /*----------------------------------End by vipin--------------------*/


//Check write Access to Directory

            if(!is_writable(dirname($output_filename))){
                    $response = Array(
                        "status" => 'error',
                        "message" => 'Can`t write cropped File'
                );	
            }else{
                $img_db= "croppedImg_".$current_date.$type;
                // resize the original image to size of editor
                $resizedImage = imagecreatetruecolor($imgW, $imgH);
                    imagecopyresampled($resizedImage, $source_image, 0, 0, 0, 0, $imgW, $imgH, $imgInitW, $imgInitH);
                // rotate the rezized image
                $rotated_image = imagerotate($resizedImage, -$angle, 0);
                // find new width & height of rotated image
                $rotated_width = imagesx($rotated_image);
                $rotated_height = imagesy($rotated_image);
                // diff between rotated & original sizes
                $dx = $rotated_width - $imgW;
                $dy = $rotated_height - $imgH;
                // crop rotated image to fit into original rezized rectangle
                $cropped_rotated_image = imagecreatetruecolor($imgW, $imgH);
                imagecolortransparent($cropped_rotated_image, imagecolorallocate($cropped_rotated_image, 0, 0, 0));
                imagecopyresampled($cropped_rotated_image, $rotated_image, 0, 0, $dx / 2, $dy / 2, $imgW, $imgH, $imgW, $imgH);
                // crop image into selected area
                $final_image = imagecreatetruecolor($cropW, $cropH);
                imagecolortransparent($final_image, imagecolorallocate($final_image, 0, 0, 0));
                imagecopyresampled($final_image, $cropped_rotated_image, 0, 0, $imgX1, $imgY1, $cropW, $cropH, $cropW, $cropH);
                // finally output png image
                //imagepng($final_image, $output_filename.$type, $png_quality);
                imagejpeg($final_image, $output_filename.$type, $jpeg_quality);
                $this->User->id=$id;
                $this->User->saveField("coverphoto",$img_db);
                
                $response = Array(
                    "status" => 'success',
                    "url" => $output_url.$type
                );
            }
//print json_encode($response);
            $this->set('res', $response);

            $this->response->type('json');

            $this->render("/Common/ajax", "ajax");
        }  
    }
    
/*
 * send email on reward(Time clean or Sober)
 */
    public function firebase() {
//       set_time_limit(0);
        $this->layout = '_inner';
        //ini_set('memory_limit', '-1');
        $loggedid = $this->Auth->user('id');
        $dataAbout = $this->User->find('first', array('conditions' => array('User.id' => $loggedid),'contain'=>false));
        $this->set('userAbout', $dataAbout);

//        // countries
//        $this->loadModel('Country');
//        $countries = $this->Country->query('select * from countries  ORDER BY `name` asc ');
//        $this->set('countries', $countries);
    }
    public function firebase_chat(){
        
    }
    public function firebase_message(){
        
    }
    public function before_varification(){
        $this->loadModel('User');
        $list=$this->User->find('all',array('conditions'=>array('User.email_varification'=>1),'fields'=>array('User.created','User.email_varification'),'recursive'=>0));
        debug($list);
        foreach($list as $lista): 
            $this->User->id=$lista['User']['id'];
        $time1=$lista['User']['created'];
      echo  $time2=date('Y-m-d H:i:s'); echo "<br/>";
      $hourdiff1=23.00;
       echo $hourdiff = round((strtotime($time2) - strtotime($time1))/3600, 1);
       if($hourdiff >= $hourdiff1):
        $this->User->delete();
       endif;
       endforeach;
                
    }
    public function postView(){
        $this->loadModel('PostView');
        if ($this->request->is('post')) {
        $loggedid = $this->Auth->user('id');
            $alldata = $this->PostView->find('all', array('conditions' => array('PostView.user_id' => $loggedid,'PostView.post_id' => $_POST['post_id'])));
            
            if(count($alldata)==0)
            {
            $data = array(
            'PostView' => array(
            'post_id' => $_POST['post_id'],'user_id' => $loggedid,'view_count' =>'1'
             )
            );
            $this->PostView->save($data);
           }

           die;
          
        }

    }
    public function commentnotification(){
        configure::write('debug',0);
       $this->loadModel('Comment');
       $this->loadModel('Post');
       $this->loadModel('User');
//       $poid=$this->Comment->find('all');
//       debug($poid);
       $poid=$this->Comment->find('all',array('conditions'=>array('Comment.email_notification'=>0),
           'order'=>'Comment.id desc','fields'=>array('Comment.id','Comment.post_id','Comment.user_id','Comment.email_notification'),'recursive'=>-1));
       $pid=  array();
       debug($poid);
       if(!empty($poid)):
       foreach($poid as $poids):
           $pot=$this->Post->find('first',array('conditions'=>array('Post.id'=>$poids['Comment']['post_id']),
           'contain'=>array('User'=>array('fields'=>array('User.id','User.email','User.firstname','User.lastname')))));
       debug($pot['User']['email']);
       $comtuser=$this->User->find('first',array('conditions'=>array('User.id'=>$poids['Comment']['user_id']),
           'fields'=>array('User.firstname','User.lastname','User.email'),'recursive'=>-1));
            $support_mail=$pot['User']['email'];
            $subject=$comtuser['User']['firstname']." has supported your post";
            $message= $comtuser['User']['firstname']." ".$comtuser['User']['lastname']." has supported your post. To view ".$comtuser['User']['firstname']." ".$comtuser['User']['lastname']." click here: <a href='".'https://mysponsers.com'.DS.'messages'.DS.'chat1'."'>".$comtuser['User']['firstname']." ".$comtuser['User']['lastname']."</a>."; 
            $Email = new CakeEmail();
            $Email->template('default'); 
            $Email->emailFormat('html');
            $Email->from(array("no-reply@mysponsers.com" => "Mysponsers"));
            $Email->to($support_mail);
            $Email->subject($subject);
            $r=$Email->send($message);
            $id=$poids['Comment']['id'];
            //$this->Comment->query("update  `comments` set `email_notification`='1' where `id`=$id");
//       debug($pot);  
//       debug($comtuser);exit;
        endforeach;    
       
       endif;
       
       //debug($pot);  
       exit;
   }
    public function autologin($id=null){
        configure::write('debug',0);
        $this->layout="ajax"; 
        $user = $this->User->findById($id);
        $user = $user['User']; 
        $sa=$this->Auth->login($user);
        if($sa):
            $response="Logged in Successfully";
                /*--Start by vipin for update the users timeZone-----*/ 
                 $ip = $_SERVER['REMOTE_ADDR'];
                 //$ip = '112.196.35.194'; 

                 $query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));

                if( isset($query) && count( $query ) >0 ) { 
                  $timezone = $query['timezone']; 
                  if($this->User->updateAll(array('tz' => "'$timezone'"),array('User.id' => $this->Session->read('Auth.User.id')))){ 
                    
                  } 
                }
                
                $this->loadModel('Usertrack');
                $geo=$this->Track->loc();
                if(empty($geo["geoplugin_city"])):
                    $cu=$this->Usertrack->find('first',array('conditions'=>array('AND'=>array(
                        'OR'=>array('Usertrack.country'=>$geo["geoplugin_countryName"]),
                        'NOT'=>array('Usertrack.city'=>"",'Usertrack.state'=>""),
                        'OR'=>array('Usertrack.country'=>$geo["geoplugin_countryName"],'Usertrack.state'=>$geo["geoplugin_regionName"]))
                        ),'fields'=>array('Usertrack.state','Usertrack.city'),'recursive'=>0));
                $this->request->data['Usertrack']['city']=$cu['Usertrack']['city'];  
                else:  
                $this->request->data['Usertrack']['city'] = $geo["geoplugin_city"];
                endif;
                if(empty($geo["geoplugin_regionName"])):
                $this->request->data['Usertrack']['state'] = $cu['Usertrack']['state'];
                else:  
                $this->request->data['Usertrack']['state'] = $geo["geoplugin_regionName"];
                endif; 
                $this->request->data['Usertrack']['country'] = $geo["geoplugin_countryName"];
                $this->request->data['Usertrack']['user_id']=$this->Auth->User('id');
                $this->request->data['Usertrack']['latitude']=$geo["geoplugin_latitude"];
                $this->request->data['Usertrack']['longitude']=$geo["geoplugin_longitude"];
                $this->request->data['Usertrack']['session_id']=session_id(); 
                $this->request->data['Usertrack']['ip']=$geo["ip"];
                $this->request->data['Usertrack']['created']=date("Y-m-d H:i:s");
				$this->request->data['Usertrack']['modified']= date("Y-m-d H:i:s");
                $user=$this->Usertrack->find('first',array('conditions'=>array(
                    'AND'=>array('Usertrack.user_id'=>$this->Auth->User('id'))),
                    'fields'=>'Usertrack.id','recursive'=>0)); 
                if(!empty($user['Usertrack']['id'])):
                    $this->request->data['Usertrack']['id']=$user['Usertrack']['id'];
					$this->request->data['Usertrack']['status']=0;
                    $this->Usertrack->save($this->request->data); 
                else: 
                    $this->Usertrack->save($this->request->data); 
                endif;
 /*--Start by vipin for update the users timeZone-----*/
                $this->redirect("/users/dashboard");
        else:    
            $this->Session->write("err_msg", "Login Failed: Invalid Login Info.");
                return $this->redirect("/");
        endif;
//      $this->render('ajax');
    }
    public function thankyou(){
        $this->layout = '_inner';
    }
}

