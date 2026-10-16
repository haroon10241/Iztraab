<?php
/* Iztraab - single-file PHP blog. Upload as index.php to any PHP hosting (PHP 7.4+). */
session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS'])]);
session_start();
const SITE='Iztraab',TAG='Thoughts, stories and ideas worth pausing for.',USER='ishaq102',MAIL='ishaqsyed93@gmail.com';
const ADS=''; // After AdSense gives you your code, put it here, e.g. 'ca-pub-1234567890123456'
const HASH='$6$EzQXmjWLMUmbI3Ry$4016glhYF3WfTiIvKgAeCtaCOzuOBls4kL721Tn2UPPXYJ7DCQAyKZIOObOmL6cAlyBhWXHj2KOiBQrNPiVnu/';

$D=__DIR__.'/data';$F="$D/posts.php";is_dir($D)||mkdir($D,0755,true);
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function posts(){global $F;if(!is_file($F))return[];$s=file_get_contents($F);return json_decode(substr($s,strpos($s,"\n")+1),true)?:[];}
function put($a){global $F;file_put_contents($F,"<?php http_response_code(403);exit;?>\n".json_encode(array_values($a),JSON_UNESCAPED_UNICODE),LOCK_EX);}
function ex($b){return mb_substr(trim(preg_replace('/\s+/',' ',strip_tags($b))),0,150).'...';}
function rich($b){return strpos($b,'<')===false?implode('',array_map(fn($t)=>'<p>'.nl2br(e($t)).'</p>',preg_split('/\R{2,}/',trim($b)))):$b;}
$_SESSION['t']??=bin2hex(random_bytes(16));
$in=!empty($_SESSION['a']);$p=$_GET['p']??'home';$msg='';$P=posts();
function ok(){return hash_equals($_SESSION['t'],$_POST['t']??'');}

/* ---------- actions ---------- */
if($_SERVER['REQUEST_METHOD']==='POST'){
  $a=$_POST['do']??'';$g=fn($k)=>trim($_POST[$k]??'');
  if($a==='login'){
    if(ok()&&hash_equals(USER,$g('u'))&&password_verify($_POST['pw']??'',HASH)){session_regenerate_id(true);$_SESSION['a']=1;header('Location: ?p=admin');exit;}
    sleep(1);$msg='Wrong username or password.';$p='admin';
  }elseif($in&&ok()){
    if($a==='save'){
      $id=$g('id');$r=['id'=>$id?:dechex(time()).bin2hex(random_bytes(2)),'title'=>$g('title'),'cat'=>$g('cat')?:'General','img'=>$g('img'),'body'=>$_POST['body']??'','date'=>date('Y-m-d')];
      $f=0;foreach($P as &$x)if($x['id']===$id){$r['date']=$x['date'];$x=$r;$f=1;}unset($x);
      if(!$f)array_unshift($P,$r);put($P);header('Location: ?p=admin');exit;
    }
    if($a==='del'){put(array_filter($P,fn($x)=>$x['id']!==$g('id')));header('Location: ?p=admin');exit;}
  }
}
if($p==='logout'){session_destroy();header('Location: ./');exit;}

/* ---------- layout ---------- */
function top($t='',$d=''){global $p;$t=$t?e($t).' | '.SITE:SITE.' | '.TAG;$d=e($d?:TAG);?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=$t?></title><meta name="description" content="<?=$d?>"><meta property="og:title" content="<?=$t?>"><meta property="og:description" content="<?=$d?>">
<?php if($p==='admin')echo'<meta name="robots" content="noindex">';if(ADS)echo'<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client='.ADS.'" crossorigin="anonymous"></script>';?>
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;600;800&family=Inter:wght@400;600&family=Noto+Nastaliq+Urdu&display=swap" rel="stylesheet">
<style>
:root{--bg:#fbfbff;--ink:#14122e;--mut:#605d78;--a:#3b2bff;--y:#ffb800;--ln:#e3e1f2;--card:#fff}
@media(prefers-color-scheme:dark){:root{--bg:#0e0d1f;--ink:#f1f0ff;--mut:#a09db8;--a:#8d84ff;--ln:#2a2848;--card:#16142c}}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:17px/1.7 Inter,system-ui,sans-serif}a{color:inherit}
:focus-visible{outline:3px solid var(--y);outline-offset:2px}
.bar{display:flex;justify-content:space-between;align-items:center;max-width:1080px;margin:auto;padding:20px}
.logo{font:800 26px Fraunces,serif;text-decoration:none;letter-spacing:-.5px}
.bar nav a,footer nav a{margin-left:20px;font-size:15px;color:var(--mut);text-decoration:none}
.bar nav a:hover,footer nav a:hover{color:var(--a)}
.hero{background:#14122e;color:#fff;text-align:center;padding:80px 20px 72px;overflow:hidden}
.hero h1{font:800 clamp(56px,14vw,160px)/1 Fraunces,serif;margin:0;letter-spacing:-3px}
.hero h1 span{display:inline-block;animation:sh .8s both}
@keyframes sh{20%{transform:translateY(-16px) rotate(-7deg)}40%{transform:translateY(7px) rotate(5deg)}60%{transform:translateY(-6px) rotate(-3deg)}80%{transform:translateY(2px)}}
@media(prefers-reduced-motion:reduce){.hero h1 span{animation:none}}
.hero p{color:#b9b5e6;font-size:19px;margin:18px 0 28px}
.sr{display:flex;gap:8px;justify-content:center;flex-wrap:wrap}.sr input{width:min(320px,100%)}
input,textarea{font:inherit;padding:12px 14px;border:1px solid var(--ln);border-radius:6px;background:var(--card);color:var(--ink)}
.btn{background:var(--a);color:#fff;border:0;padding:11px 22px;border-radius:99px;cursor:pointer;text-decoration:none;display:inline-block;font:600 16px Inter,sans-serif}
.btn.s{background:none;color:var(--a);border:1px solid var(--ln)}
.w{max-width:1080px;margin:auto;padding:0 20px}
.g{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:24px;margin:44px 0}
.c{background:var(--card);border:1px solid var(--ln);border-radius:6px;overflow:hidden;text-decoration:none;display:flex;flex-direction:column;transition:border-color .2s}
.c:hover{border-color:var(--a)}
.im{height:180px;background:linear-gradient(135deg,hsl(var(--h) 80% 55%),hsl(calc(var(--h) + 60) 90% 62%)) center/cover}
.f{grid-column:1/-1;flex-direction:row}.f .im{width:52%;height:auto;min-height:300px}.f h2{font-size:34px!important}
@media(max-width:700px){.f{flex-direction:column}.f .im{width:100%;min-height:190px}}
.b{padding:22px}.c h2{font:600 23px/1.3 Fraunces,'Noto Nastaliq Urdu',serif;margin:12px 0 8px}.c p{color:var(--mut);margin:0;font-size:15px}
.pill{display:inline-block;background:var(--y);color:#14122e;font-size:13px;font-weight:600;padding:2px 12px;border-radius:99px;line-height:1.6}
.m{color:var(--mut);font-size:14px;margin-top:12px}
.art{max-width:720px;margin:48px auto;padding:0 20px;display:block}
.art h1{font:800 clamp(34px,6vw,52px)/1.15 Fraunces,'Noto Nastaliq Urdu',serif;letter-spacing:-1px;margin:14px 0 8px}
.art h2{font:600 24px Fraunces,serif;margin:36px 0 4px}
.t{font:400 19px/1.9 Fraunces,'Noto Nastaliq Urdu',serif}.t[dir=rtl]{line-height:2.4}.art img{max-width:100%;border-radius:6px;margin:16px 0}
.fm label{display:block;font-weight:600;font-size:15px}.fm input,.fm textarea{width:100%;margin:6px 0 18px}
.row{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:14px 0;border-bottom:1px solid var(--ln)}
.row form{display:inline}.er{color:#d6204a;font-weight:600}.e{text-align:center;color:var(--mut);padding:60px 0}
footer{border-top:1px solid var(--ln);margin-top:64px;padding:32px 20px;text-align:center;color:var(--mut);font-size:15px}
footer nav{margin:12px 0}footer nav a:first-child{margin-left:0}
</style></head><body>
<header class="bar"><a class="logo" href="./">Iztraab</a><nav><a href="./">Home</a><a href="?p=about">About</a><a href="?p=contact">Contact</a></nav></header>
<?php }
function bot(){?>
<footer><b>Iztraab</b><nav><a href="?p=about">About</a><a href="?p=contact">Contact</a><a href="?p=privacy">Privacy Policy</a><a href="?p=terms">Terms &amp; Disclaimer</a></nav>&copy; <?=date('Y')?> Iztraab. All rights reserved.</footer></body></html>
<?php }
function card($x,$f){$h=crc32($x['id'])%360;$bg=$x['img']?";background-image:url('".e($x['img'])."')":'';
  echo'<a class="c'.($f?' f':'').'" href="?p=post&id='.e($x['id']).'"><div class="im" style="--h:'.$h.$bg.'"></div><div class="b"><span class="pill">'.e($x['cat']).'</span><h2 dir="auto">'.e($x['title']).'</h2><p dir="auto">'.e(ex($x['body'])).'</p><div class="m">'.date('M j, Y',strtotime($x['date'])).' &middot; '.max(1,round(count(preg_split('/\s+/',trim(strip_tags($x['body']))))/200)).' min read</div></div></a>';}

/* ---------- static pages (edit text as you like) ---------- */
$M='<a href="mailto:'.MAIL.'">'.MAIL.'</a>';
$pages=[
'about'=>['About Iztraab','<p>Iztraab means restlessness: the feeling of having something to say before you know how to say it. This blog is where those thoughts become short, honest writing.</p><p>Every article is written by the site owner, read through before it goes live, and published to be useful, original and clear. There are no automated or copied posts here.</p><p>Feedback or questions? Write to '.$M.'.</p>'],
'contact'=>['Contact','<p>We read every message. For feedback, corrections, collaboration or any question, email us at '.$M.'. We usually reply within a few days.</p>'],
'privacy'=>['Privacy Policy','<p>Last updated: October 2026. This policy explains what information Iztraab collects and how it is used.</p>
<h2>Information we collect</h2><p>Visitors do not need an account to read Iztraab. Like most websites, our server automatically records standard log data such as IP address, browser type, device, referring page and pages visited. If you email us, we keep your address and message only to reply to you.</p>
<h2>Cookies and advertising</h2><p>Iztraab may display ads served by Google AdSense. Third-party vendors, including Google, use cookies to serve ads based on your prior visits to this and other websites. Google\'s use of advertising cookies enables it and its partners to serve ads to you based on your visit to this site and/or other sites on the Internet.</p><p>You can opt out of personalized advertising at <a href="https://adssettings.google.com" rel="noopener">Google Ads Settings</a> or <a href="https://www.aboutads.info" rel="noopener">aboutads.info</a>. You can also block or delete cookies in your browser settings.</p>
<h2>Third-party links</h2><p>Articles may link to other websites. We are not responsible for their content or privacy practices.</p>
<h2>Children</h2><p>Iztraab is not directed to children under 13, and we do not knowingly collect information from them.</p>
<h2>Your choices</h2><p>To ask about or request removal of any information you sent us, email '.$M.'.</p>
<h2>Changes</h2><p>We may update this policy from time to time. Changes take effect when posted on this page.</p>
<h2>Contact</h2><p>Questions about this policy: '.$M.'.</p>'],
'terms'=>['Terms &amp; Disclaimer','<p>By using Iztraab you agree to these terms.</p><h2>Content</h2><p>All articles, text and design on Iztraab belong to the site owner unless stated otherwise. You may quote short excerpts with a link back to the original article. Please do not republish full articles without written permission.</p><h2>Disclaimer</h2><p>Content is shared for general information and personal reflection only. It is not professional, medical, legal or financial advice. We try to keep everything accurate but make no guarantee of completeness.</p><h2>External links and ads</h2><p>We are not responsible for third-party sites or advertisements shown on Iztraab.</p><h2>Changes</h2><p>We may update these terms at any time. Questions: '.$M.'.</p>']];

/* ---------- routes ---------- */
if($p==='sitemap'){header('Content-Type:application/xml');$u=(empty($_SERVER['HTTPS'])?'http':'https').'://'.$_SERVER['HTTP_HOST'].strtok($_SERVER['REQUEST_URI'],'?');
  echo'<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>'.e($u).'</loc></url>';
  foreach(['about','contact','privacy','terms']as$k)echo'<url><loc>'.e("$u?p=$k").'</loc></url>';
  foreach($P as$x)echo'<url><loc>'.e("$u?p=post&id=$x[id]").'</loc><lastmod>'.$x['date'].'</lastmod></url>';exit('</urlset>');}

if(isset($pages[$p])){[$t,$h]=$pages[$p];top(strip_tags(html_entity_decode($t)));echo'<main class="art"><h1>'.$t.'</h1>'.$h.'</main>';bot();exit;}

if($p==='post'){
  foreach($P as $x)if($x['id']===($_GET['id']??'')){top($x['title'],ex($x['body']));?>
<article class="art"><span class="pill"><?=e($x['cat'])?></span><h1 dir="auto"><?=e($x['title'])?></h1>
<div class="m"><?=date('F j, Y',strtotime($x['date']))?><?php if($in)echo' &middot; <a href="?p=admin&e='.e($x['id']).'">Edit</a>';?></div>
<?php if($x['img'])echo'<img src="'.e($x['img']).'" alt="'.e($x['title']).'">';?>
<div class="t" dir="auto"><?=rich($x['body'])?></div><p><a href="./">&larr; All articles</a></p></article>
<?php bot();exit;}
  http_response_code(404);top('Not found');echo'<main class="art"><h1>Page not found</h1><p><a href="./">Back to all articles</a></p></main>';bot();exit;}

if($p==='admin'){
  top('Admin');echo'<main class="art">';
  if(!$in){?>
<h1>Admin login</h1><?php if($msg)echo'<p class="er">'.e($msg).'</p>';?>
<form method="post" class="fm"><input type="hidden" name="do" value="login"><input type="hidden" name="t" value="<?=$_SESSION['t']?>">
<label>Username<input name="u" autocomplete="username" required></label><label>Password<input type="password" name="pw" autocomplete="current-password" required></label><button class="btn">Log in</button></form>
<?php }else{
    $ed=['id'=>'','title'=>'','cat'=>'','img'=>'','body'=>''];$edit=isset($_GET['new']);
    if(isset($_GET['e']))foreach($P as $x)if($x['id']===$_GET['e']){$ed=$x;$edit=true;}
    if($edit){?>
<h1><?=$ed['id']?'Edit post':'New post'?></h1>
<form method="post" class="fm"><input type="hidden" name="do" value="save"><input type="hidden" name="t" value="<?=$_SESSION['t']?>"><input type="hidden" name="id" value="<?=e($ed['id'])?>">
<label>Title<input name="title" value="<?=e($ed['title'])?>" required></label>
<label>Category<input name="cat" value="<?=e($ed['cat'])?>" placeholder="e.g. Life, Tech, Poetry"></label>
<label>Cover image URL (optional)<input name="img" value="<?=e($ed['img'])?>" placeholder="https://..."></label>
<label>Article<textarea name="body" rows="16" required><?=e($ed['body'])?></textarea></label>
<p class="m">Plain text works: leave a blank line for a new paragraph. You can also paste HTML (headings, images, links). Urdu text displays right-to-left automatically.</p>
<button class="btn">Publish</button> <a class="btn s" href="?p=admin">Cancel</a></form>
<?php }else{?>
<h1>Your posts</h1><p><a class="btn" href="?p=admin&new=1">New post</a> <a class="btn s" href="?p=logout">Log out</a></p>
<?php if(!$P)echo'<p class="e">No posts yet. Write your first one.</p>';
      foreach($P as $x){?><div class="row"><span><a href="?p=post&id=<?=e($x['id'])?>" dir="auto"><?=e($x['title'])?></a><br><small class="m"><?=$x['date']?> &middot; <?=e($x['cat'])?></small></span>
<span><a class="btn s" href="?p=admin&e=<?=e($x['id'])?>">Edit</a> <form method="post" onsubmit="return confirm('Delete this post?')"><input type="hidden" name="do" value="del"><input type="hidden" name="t" value="<?=$_SESSION['t']?>"><input type="hidden" name="id" value="<?=e($x['id'])?>"><button class="btn s">Delete</button></form></span></div>
<?php }}}
  echo'</main>';bot();exit;}

/* home */
$q=trim($_GET['q']??'');
$L=$q===''?$P:array_values(array_filter($P,fn($x)=>stripos($x['title'].' '.strip_tags($x['body']),$q)!==false));
top();?>
<section class="hero"><h1 aria-label="Iztraab"><?php foreach(str_split(SITE)as$i=>$c)echo'<span aria-hidden="true" style="animation-delay:'.($i*.07).'s">'.$c.'</span>';?></h1>
<p><?=e(TAG)?></p><form class="sr"><input name="q" value="<?=e($q)?>" placeholder="Search articles" aria-label="Search articles"><button class="btn">Search</button></form></section>
<main class="w"><?php if(!$L)echo'<p class="e">'.($q===''?'No articles published yet. Check back soon.':'No articles match your search.').'</p>';
else{echo'<div class="g">';foreach($L as $i=>$x)card($x,$i===0&&$q==='');echo'</div>';}?></main>
<?php bot();
