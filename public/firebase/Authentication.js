 // Initialize Firebase
var config = {
    apiKey: "AIzaSyDsjUkRNPxpOVFtBtwi3EBzChTcopoH9Z0",
    authDomain: "liangyu71220.firebaseapp.com",
    databaseURL: "https://liangyu71220.firebaseio.com",
    projectId: "liangyu71220",
    storageBucket: "",
    messagingSenderId: "689974222373"
};
firebase.initializeApp(config);

//Email/Pwd註冊
var account = document.getElementById("account");
var pwd = document.getElementById("pwd");
var registerSmtBtn = document.getElementById("registerSmtBtn");
registerSmtBtn.addEventListener("click", function(){

    console.log(account.value);
    firebase.auth().createUserWithEmailAndPassword(account.value, pwd.value).catch(function(error) {
    // Handle Errors here.
    var errorCode = error.code;
    var errorMsg = error.message;
    console.log(errorMsg);
  });
},false);

//登入
var accountL = document.getElementById("accountL");
var pwdL = document.getElementById("pwdL");
var loginSmtBtn = document.getElementById("loginSmtBtn");
loginSmtBtn.addEventListener("click",function(){
    console.log(accountL.value);
    firebase.auth().signInWithEmailAndPassword(accountL.value, pwdL.value).catch(function(error) {
    // Handle Errors here.
    var errorCode = error.code;
    var errorMessage = error.message;
    console.log(errorMessage);
  })
},false);

//登出
var signoutSmtBtn = document.getElementById("signoutSmtBtn");
signoutSmtBtn.addEventListener("click",function(){
    firebase.auth().signOut().then(function() {
        console.log("User sign out!");
    }, function(error) {
        console.log("User sign out error!");
    })
},false);

//Email驗證
var verifyBtn = document.getElementById("verifyBtn");
verifyBtn.addEventListener("click",function(){
  user.sendEmailVerification();
    console.log("驗證信寄出");
},false);

//更改密碼
var chgPwdEmail = document.getElementById("chgPwdEmail");
var chgPwdEmailBtn = document.getElementById("chgPwdEmailBtn");
chgPwdEmailBtn.addEventListener("click",function(){


    firebase.auth().sendPasswordResetEmail(chgPwdEmail.value).then(function() {
        // Email sent.
        console.log("更改密碼Email已發送");
        chgPwdEmail.value = "";
    }, function(error) {
        // An error happened.
        console.error("更改密碼",error);
    });


},false);

//查看目前登入狀況
var user;
firebase.auth().onAuthStateChanged(function(user) {

  if (user) {
    user = user;
    console.log(user.email + " 登入");
    if (user.emailVerified == true) {
        console.log("已驗証!");
    }
  } else {
    user = null;
    console.log("User is not logined yet.");
  }
});

//如果使用者操作了更改密碼、刪除帳號、更改信箱等，需要再次驗證
/*var user = firebase.auth().currentUser;
var credential = firebase.auth().EmailAuthProvider.credential(
    user.email,
  //password from user
)*/

var provider = new firebase.auth.FacebookAuthProvider();
//所需授權的Scope
//查閱 https://developers.facebook.com/docs/facebook-login/permissions
provider.addScope('user_birthday');
provider.setCustomParameters({
  'display': 'popup'
});


//使用Popup註冊FB方式
var fbLoginBtn = document.getElementById("fbLoginBtn");
fbLoginBtn.addEventListener("click",function(){
  firebase.auth().signInWithPopup(provider).then(function(result) {
    // 取得FB Token，可以使用於FB API中
    var token = result.credential.accessToken;
    // 使用者資料
    var FBUser = result.user;
    console.log(FBUser);
  }).catch(function(error) {
    //處理 帳號已經存在於其他登入方式時
    if (error.code === 'auth/account-exists-with-different-credential') {
      //取得credential
      var pendingCred = error.credential;
      // The provider account's email address.
      var email = error.email;
      console.log("FB登入錯誤-使用者信箱：",email);
      // 取得當初此Email的登入方式
      firebase.auth().fetchProvidersForEmail(email).then(function(providers) {
        // 如果使用者有多個登入方式的話
        console.log("FB登入錯誤-其他登入方式：",providers);
        if (providers[0] === 'password') {
          // 如果使用者用密碼登入，要求使用者輸入密碼
          var password = promptUserForPassword(); // TODO: 實作 promptUserForPassword.
          firebase.auth().signInWithEmailAndPassword(email, password).then(function(user) {
            // Step 4a.
            return user.link(pendingCred);
          }).then(function() {
            // 成功連結
            goToApp();
          });
          return;
        }
        // 如果是其他登入方式，必須取得該登入方式,同時提供相對應的Provider
        // TODO: implement getProviderForProviderId.
        var provider = getProviderForProviderId(providers[0]);
        // 此時你必須讓使用者了解到 他曾經用其他方式登入過
        // Note: 瀏覽器通常會擋住跳出頁面，所以在現實狀況下，最好有個"請繼續"按鈕觸發新的註冊頁面
        // 可以參考 https://fir-ui-demo-84a6c.firebaseapp.com/
        auth.signInWithPopup(provider).then(function(result) {
          // 如果使用者用不同Email登入同一個帳號，這樣Firebase是無法擋住的
          // Step 4b.
          // 連結回原本的credential
          result.user.link(pendingCred).then(function() {
            // 成功連結
            goToApp();
          });
        });
      });
    }
  });
},false);

// promptUserForPassword(){
//     var pwd = prompt("Please enter your password");
//     if (pwd != null) {
//         return pwd;
//     }
// }

// goToApp(){

// }
