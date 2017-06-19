<?php
App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');
App::uses('CakeEmail', 'Network/Email');
class TestsController extends AppController {
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->Allow(array('index'));
    }
    public function index() {
        $this->layout='ajax';
        $indexInfo['description'] = "App user Registration(post method)(2-d array) ";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/signup";
        $indexInfo['parameters'] = '<b>data[User][firstname] - </b>Full Name (Real or Anonymous)<br>
		<b>data[User][sex]</b>-Male/Female<br>
                <b>data[User][mont]</b>-month number Ex. 1,2 ..etc<br>
                <b>data[User][day]</b>-Days number Ex. 1,2,3 .. etc<br>
                <b>data[User][yr]</b>-Year<br>
                <b>data[User][addict_alco_type]</b>-Addict/Alcoholic<br>
                <b>data[User][email]</b>-Email id<br>
                <b>data[User][cemail]</b>-Confirm Email<br>
                <b>data[User][password]</b>-password<br>';
        $indexarr[] = $indexInfo;
        
        $indexInfo['description'] = "App user login(post method)(2-d array) ";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/login";
        $indexInfo['parameters'] = '<b>data[User][email] - </b>email id<br>
		<b>data[User][password]</b>-User password<br>
                <b>data[User][device_id]</b>-device id<br>
                <b>data[User][device_token]</b>-device token id<br>';
        $indexarr[] = $indexInfo;
        
        $indexInfo['description'] = "App Forgot password ";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/forgetpassword";
        $indexInfo['parameters'] = '<b>data[User][email] - </b>user id<br> ';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Rest password";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/reset/{token}";
        $indexInfo['parameters'] = '<b>data[User][password] - </b> password <br> 
                <b>data[User][password_confirm] - </b>Confirm password';
        $indexarr[] = $indexInfo;
        
        $indexInfo['description'] = "Count Up timer";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/timer_countup/{user id}";
        $indexInfo['parameters'] = '';
        $indexarr[] = $indexInfo;
        
        $indexInfo['description'] = "Post text, video or images";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/homepostphoto/{user id}";
        $indexInfo['parameters'] = '<b>data[Post][post] - </b> post here content,youtube url , iframe  <br> 
                <b>data[Post][photo][] - </b>upload images and video <br/>
                <b>data[Post][width][] - </b>width images or video <br/> 
                <b>data[Post][height][] - </b>Height for images or video';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Myposts";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/mypost/{user id}/{page number}";
        $indexInfo['parameters'] = 'All post list by user id <br>Ex:- </br>ios/mypost/81/1';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Public Post";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/publicpost/{user id}/{page number}";
        $indexInfo['parameters'] = 'All post list by user id <br>Ex:- </br>ios/publicpost/81/1';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Support Member i support Post";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/isupport/{user id}/{page number}";
        $indexInfo['parameters'] = 'Support Member I support by user id <br>Ex:- </br>ios/isupport/81/1';
        $indexarr[] = $indexInfo;
        
        $indexInfo['description'] = "Support";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/suppoter_unsuppoter/{user id}/{post id}";
        $indexInfo['parameters'] = '';
        $indexarr[] = $indexInfo; 
        
        $indexInfo['description'] = "Comment by post id and user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/commentlist/{post id}/{user id}";
        $indexInfo['parameters'] = '<b>data[Comment][comment] - </b> Comment text and imoji ';
        $indexarr[] = $indexInfo;

        // $indexInfo['description'] = "Public Post";
        // $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "comments/public_post/{user id}";
        // $indexInfo['parameters'] = '';
        // $indexarr[] = $indexInfo; 

        $indexInfo['description'] = "Emoji";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/emooji";
        $indexInfo['parameters'] = '';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Post Update by post id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/post_edit/{post id}";
        $indexInfo['parameters'] = '<b>data[Post][post] - </b> Update text  <br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Post Delete by post id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/post_delete/{post id}";
        $indexInfo['parameters'] = '';
        $indexarr[] = $indexInfo;

        // $indexInfo['description'] = "Public Post";
        // $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "comments/publicpost/{user id}/{page number}";
        // $indexInfo['parameters'] = '<br>Ex:- </br>ios/publicpost/81/1';
        // $indexarr[] = $indexInfo;

        $indexInfo['description'] = "About";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/about";
        $indexInfo['parameters'] = '';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "User info and Edit Profile by User id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/edit_profile/{user id}";
        $indexInfo['parameters'] = '<b>data[User][firstname] - </b>Full Name  <br> 
                <b>data[User][sex] - </b>Male/Female <br> 
                <b>data[User][dob] - </b>y-m-d <br> 
                <b>ddata[User][email] - </b> Email  <br> 
                <b>data[User][addtimezone] - </b>d/m/y <br> 
                <b>data[User][motto] - </b> Write a view <br> 
                <b>data[User][country] - </b>Country Name <br> 
                <b>data[User][state] - </b>State name <br> 
                <b>data[User][city] - </b>City Name <br> 
                <b>data[User][home] - </b>Home town <br> 
                <b>data[User][highschool] - </b>Highschool <br> 
                <b>data[User][college] - </b>College Name <br> 
                <b>data[User][company] - </b>Company <br> 
                <b>data[User][designation] - </b>Designation <br>
                <b>data[User][phone] - </b>phone number <br> 
                <b>data[User][cell_no] - </b> mobile no <br> 
                <b>ddata[User][home] - </b> home <br> 
                <b>data[User][born_address] - </b>born address <br> 
                <b>data[User][raised_child] - </b> raised child <br> 
                <b>data[User][where_live] - </b>where u live <br> 
                <b>data[User][where_lived] - </b>where u lived in <br> 
                <b>data[User][where_visited] - </b>where visited <br> 
                <b>data[User][family_member] - </b>family member <br> 
                <b>data[User][relation_ship] - </b>relation ship <br> 
                <b>data[User][workplace_company] - </b>workplace company <br> 
                <b>data[User][addict_alco_type] - </b>addict alco type <br> 
                <b>data[User][addtimezone] - </b>addtimezone <br> 
                <b>data[User][time_addict_alco] - </b>time addict alco <br> 
                <b>data[User][highschool] - </b>highschool <br> 
                <b>ddata[User][college] - </b> college  <br> 
                <b>data[User][company] - </b>company <br> 
                <b>data[User][position] - </b> position <br> 
                <b>data[User][city_town] - </b>city town <br> 
                <b>data[User][color] - </b>color <br> 
                <b>data[User][workplace_description] - </b>workplace description <br> 
                <b>data[User][professional_skill] - </b>professional skill <br> 
                <b>data[User][college_passout] - </b>college passout <br> 
                <b>data[User][highschool_passout] - </b>highschool passout<br> 
                <b>data[User][current_city_town] - </b>current city town <br> 
                <b>data[User][yourhometown] - </b>your home town <br> 
                <b>data[User][your_places] - </b>your places <br>
                <b>data[User][meeting] - </b>meeting <br>  
                <b>data[User][about_you] - </b>about you <br> ';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "upload profile pic by user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/profileimg_save_to_file/{user id}";
        $indexInfo['parameters'] = '<b> img - </b> type => file(image) <br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "update coverphoto/landscape by user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/changecoverphoto/{user id}";
        $indexInfo['parameters'] = '<b> img - </b> type => file(image) <br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Search user by firstname, lastname , city, state";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/autosearchtop";
        $indexInfo['parameters'] = '<b> data[keyword] - </b>name / city / state<br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Terms and privacy page by user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/terms/{user id}";
        $indexInfo['parameters'] = '';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Contact by user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/coutactus/{user id}";
        $indexInfo['parameters'] = '<b> data[Contact][contact_reason] - </b> reason for contact<br>
        <b> data[Contact][email] - </b> Email<br>
        <b> data[Contact][subject] - </b> subject<br>
        <b> data[Contact][massege] - </b> Text<br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Become A Sponser";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/becomeAsponser";
        $indexInfo['parameters'] = '<b> data[email] - </b> Email<br> 
        <b> data[subject] - </b> subject<br>
        <b> data[message] - </b> Text<br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Help by user id ";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/help/{user id}";
        $indexInfo['parameters'] = '<b>data[Help][type] - </b>Type Of Issue<br>
        <b>data[Help][email] </b>- Email Address<br> 
                <b>data[Help][description]</b>- description<br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Media by user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/photo/{user id}";
        $indexInfo['parameters'] = '';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "live meetings group list";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/livemeetinglist";
        $indexInfo['parameters'] = '';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "live meetings by group id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/livemeeting/{group id}/{user id}";
        $indexInfo['parameters'] = '<b>data[Livechat][chat] - </b>Type text here<br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Support group list by user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/supports/{user id}";
        $indexInfo['parameters'] = '<b>  - </b><br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Online user list by user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/onlinemember/{user id}";
        $indexInfo['parameters'] = '<b>  - </b><br>';
        $indexarr[] = $indexInfo;
 

        $indexInfo['description'] = "Share Post in homepage by post id ";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/share_post_onhomepage/{login user id}/{post id}";
        $indexInfo['parameters'] = '<b>data[message] - </b> Message (optional)<br>
                                    <b>Ex:-  </b> ios/share_post_onhomepage/13267/15418<br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Share Post in friends page by post id ";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/share_post_onhomepage/{login user id}/{post id}/{share with user id}";
        $indexInfo['parameters'] = '<b>data[message] - </b> Message (optional)<br>
                                    <b>Ex:-  </b> ios/share_post_onhomepage/13267/15418/81<br>';
        $indexarr[] = $indexInfo;
		
		$indexInfo['description'] = "Livmeeting Add by Meeting idand user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/livechat_add/{Meeting id}/{ user id}";
        $indexInfo['parameters'] = '<b>data[Livechat][chat] - </b> Text<br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Sponser list api user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/needsponsers/{ user id}";
        $indexInfo['parameters'] = '<br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Friend information by id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/viewmyfriend/{friend id}/{user id}";
        $indexInfo['parameters'] = '<br><b>Note: </b> friend_support_status => 0 friend unsupport,  friend_support_status => 1 friend support <br/>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "FAQ";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/faq";
        $indexInfo['parameters'] = '<br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "support OR unsupport group by user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/groupsupport_unsupport/{ user id}";
        $indexInfo['parameters'] = '<b>data[Support][touserid] - </b> user id what login user wants to support<br> 
        <b>data[Support][status] - </b> for support => 1 , for unsupport => 0 <br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "block support group user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/blockmysupport/{user id}";
        $indexInfo['parameters'] = '<b>data[Support][touserid] - </b> user id to block that user<br>
        <b>data[Support][support] - </b>block<br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Change password user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/changepassword/{ user id}";
        $indexInfo['parameters'] = '<b>data[User][old_password] - </b> old password<br>
        <b>data[User][new_password] - </b> New Password<br>
        <b>data[User][cpassword] - </b> Confirm Password<br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Delete supporter by user id and friend id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/delete_supporter/{user id}/{friend id}";
        $indexInfo['parameters'] = '<br>';
        $indexarr[] = $indexInfo; 

        $indexInfo['description'] = "/*================ Messages ==========*/<br/>Conversation friend list by user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/chat_load/{user id}";
        $indexInfo['parameters'] = '<br/>';
        $indexarr[] = $indexInfo; 

        $indexInfo['description'] = "count unread Messages by login user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/chatunread_mesagecount/{user id}";
        $indexInfo['parameters'] = '<b>data[timezone] - </b> Ex:- Asia/Kolkata<br>
        <b>data[localdate] - </b>Ex:- 2016-05-11 21:13:57<br/>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Start typing request on Messages by login user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/chat1_typing/{user id}";
        $indexInfo['parameters'] = '<b>data[timezone] - </b> Ex:- Asia/Kolkata<br>
        <b>data[datetime] - </b>Ex:- 2016-05-11 21:13:57<br/>
        <b>data[friend_id] - </b> friend id <br/>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "previous chart list by fiend id, first click or after first click status, timezone, date and time, login user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/chat1_list/{friendid-friendid}/{0|1}/{0|1}/{timezone}/{date & time}/userid";
        $indexInfo['parameters'] = '<b>Ex: - </b>  chat1_list/12101-12101/0/0/Asia-Kolkata/2017-4-1 0:40:18/237<br>
        <b>Note:-  - </b> 1--> first request 0--> After first request<br/>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Delete message by message id, login user id and status 0";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/chat1_delete/{message id}/userid/{status}";
        $indexInfo['parameters'] = '<b>Ex: - </b>  chat1_delete/20818/236/0<br>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Post only Messages by login user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/msg_post/{user id}";
        $indexInfo['parameters'] = '<b>data[timezone] - </b> Ex:- Asia/Kolkata<br>
        <b>data[created] - </b>Ex:- 2016-05-11 21:13:57<br/>
        <b>data[frnd_id] - </b> friend id <br/>
        <b>data[msg] - </b> text message <br/>
        <b>data[msg_create] - </b> April 1, 2017 10:26 PM <br/>';
        $indexarr[] = $indexInfo;

        $indexInfo['description'] = "Post image with text Messages by login user id";
        $indexInfo['url'] = FULL_BASE_URL . $this->webroot . "ios/chat1_photo/{user id}";
        $indexInfo['parameters'] = '<b>data[timezone] - </b> Ex:- Asia/Kolkata<br>
        <b>data[created] - </b>Ex:- 2016-05-11 21:13:57<br/>
        <b>data[friend_id] - </b> friend id <br/>
        <b>data[msg] - </b> text message <br/>
        <b>data[filetoupload] - </b> type=> file image file <br/>';
        $indexarr[] = $indexInfo;

         $this->set('IndexDetail', $indexarr);
    }
    
}
?>