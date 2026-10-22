const SITE='Iztraab',ADS=''; // after AdSense gives you the code: ADS='ca-pub-1234567890123456'
const $=s=>document.querySelector(s);
const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const strip=h=>h.replace(/<[^>]*>/g,' ').replace(/\s+/g,' ').trim();
const ex=b=>strip(b).slice(0,150)+'...';
const rich=b=>b.includes('<')?b:b.trim().split(/\n{2,}/).map(t=>'<p>'+esc(t).replace(/\n/g,'<br>')+'</p>').join('');
const posts=async()=>{try{return await(await fetch('posts.json?'+Date.now())).json()}catch{return[]}};
const FB={project:'',key:''}; // Firebase settings for likes, comments and views (see setup steps)
const JH={'Content-Type':'application/json'},DB=()=>`projects/${FB.project}/databases/(default)/documents`;
const fs=async(p,o,q='')=>{const r=await fetch(`https://firestore.googleapis.com/v1/${DB()}${p}?${q}${FB.key?'&key='+FB.key:''}`,o);if(!r.ok)throw 0;return r.json()};
const bump=(id,f)=>fs(':commit',{method:'POST',headers:JH,body:JSON.stringify({writes:[{update:{name:`${DB()}/stats/${id}`},updateMask:{fieldPaths:[]},updateTransforms:[{fieldPath:f,increment:{integerValue:'1'}}]}]})});
const num=d=>+(d?.integerValue||0);
async function statsFB(){const o={};if(!FB.project)return o;try{((await fs('/stats',undefined,'pageSize=300')).documents||[]).forEach(d=>o[d.name.split('/').pop()]={v:num(d.fields?.views),l:num(d.fields?.likes)})}catch{}return o}
async function socialFB(x,m){
  if(!FB.project)return;const id=x.id,s=document.createElement('section');s.className='soc';
  s.innerHTML=`<div class="act"><button class="btn s" id="lk"></button><span class="m" id="vw"></span></div><h2>Comments</h2><form class="fm" id="cf"><input id="cn" maxlength="40" placeholder="Your name" required><textarea id="ct" rows="3" maxlength="1000" placeholder="Write a comment..." required></textarea><button class="btn">Post comment</button></form><div id="cl"></div>`;
  m.appendChild(s);let st={v:0,l:0};
  const upd=()=>{$('#lk').textContent=(localStorage['l_'+id]?'♥ ':'♡ ')+st.l;$('#vw').textContent='👁 '+st.v+' views'};
  try{const d=(await fs('/stats/'+id)).fields;st={v:num(d?.views),l:num(d?.likes)}}catch{}
  upd();
  if(!localStorage['v_'+id]){localStorage['v_'+id]=1;bump(id,'views').then(()=>{st.v++;upd()}).catch(()=>{})}
  $('#lk').onclick=()=>{if(localStorage['l_'+id])return;localStorage['l_'+id]=1;st.l++;upd();bump(id,'likes').catch(()=>{})};
  const load=async()=>{try{
    const j=await fs(':runQuery',{method:'POST',headers:JH,body:JSON.stringify({structuredQuery:{from:[{collectionId:'comments'}],where:{fieldFilter:{field:{fieldPath:'post'},op:'EQUAL',value:{stringValue:id}}},limit:100}})});
    const L=j.filter(r=>r.document).map(r=>({n:r.document.fields.name.stringValue,t:r.document.fields.text.stringValue,s:num(r.document.fields.ts)})).sort((a,b)=>b.s-a.s);
    $('#cl').innerHTML=L.length?L.map(c=>`<div class="row"><div><b>${esc(c.n)}</b> <small class="m">${new Date(c.s).toLocaleDateString()}</small><p dir="auto" style="margin:4px 0 0">${esc(c.t)}</p></div></div>`).join(''):'<p class="m">No comments yet. Be the first.</p>'}catch{$('#cl').innerHTML='<p class="m">Comments could not be loaded.</p>'}};
  $('#cf').onsubmit=async e=>{e.preventDefault();if(Date.now()-(+localStorage.lc||0)<30000)return alert('Please wait a little before commenting again.');
    try{await fs('/comments',{method:'POST',headers:JH,body:JSON.stringify({fields:{post:{stringValue:id},name:{stringValue:$('#cn').value.trim()},text:{stringValue:$('#ct').value.trim()},ts:{integerValue:String(Date.now())}}})});localStorage.lc=Date.now();$('#ct').value='';load()}catch{alert('Could not post your comment. Please try again.')}};
  load();
}
if(ADS){const s=document.createElement('script');s.async=true;s.crossOrigin='anonymous';s.src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client='+ADS;document.head.appendChild(s)}
document.body.insertAdjacentHTML('afterbegin','<header class="bar"><a class="logo" href="./">Iztraab</a><nav><a href="./">Home</a><a href="about.html">About</a><a href="contact.html">Contact</a></nav></header>');
document.body.insertAdjacentHTML('beforeend','<footer><b>Iztraab</b><nav><a href="about.html">About</a><a href="contact.html">Contact</a><a href="privacy.html">Privacy Policy</a><a href="terms.html">Terms &amp; Disclaimer</a></nav>&copy; '+new Date().getFullYear()+' Iztraab. All rights reserved.</footer>');
const MAIL='ishaqsyed93@gmail.com',NS='iztraab-k7q2x9'; // NS = private counter group name, keep it unique
const cnt=async(k,up)=>{try{const j=await(await fetch(`https://api.counterapi.dev/v1/${NS}/${k}${up?'/up':''}`)).json();return +(j.count??j.value??0)}catch{return 0}};
async function stats(P){if(FB.project)return statsFB();const o={};await Promise.all((P||[]).map(async p=>{o[p.id]={v:await cnt('v-'+p.id),l:await cnt('l-'+p.id)}}));return o}
async function social(x,m){
  if(FB.project)return socialFB(x,m);
  const id=x.id,s=document.createElement('section');s.className='soc';
  s.innerHTML=`<div class="act"><button class="btn s" id="lk"></button><span class="m" id="vw"></span></div><a class="btn s" href="mailto:${MAIL}?subject=${encodeURIComponent('Comment on: '+x.title)}">Send your thoughts by email</a>`;
  m.appendChild(s);let st={v:0,l:0};
  const upd=()=>{$('#lk').textContent=(localStorage['l_'+id]?'♥ ':'♡ ')+st.l;$('#vw').textContent='👁 '+st.v+' views'};
  upd();[st.v,st.l]=await Promise.all([cnt('v-'+id,!localStorage['v_'+id]),cnt('l-'+id)]);localStorage['v_'+id]=1;upd();
  $('#lk').onclick=()=>{if(localStorage['l_'+id])return;localStorage['l_'+id]=1;st.l++;upd();cnt('l-'+id,1)};
}
const pg=document.body.dataset.page;
const card=(x,f,S={})=>`<a class="c${f?' f':''}" href="post.html?id=${encodeURIComponent(x.id)}"><div class="im" style="--h:${[...x.id].reduce((a,c)=>a+c.charCodeAt(0)*7,0)%360}${x.img?`;background-image:url('${esc(x.img)}')`:''}"></div><div class="b"><span class="pill">${esc(x.cat)}</span><h2 dir="auto">${esc(x.title)}</h2><p dir="auto">${esc(ex(x.body))}</p><div class="m">${x.date} &middot; ${Math.max(1,Math.round(strip(x.body).split(' ').length/200))} min read${S[x.id]?` &middot; 👁 ${S[x.id].v} &middot; ♥ ${S[x.id].l}`:''}</div></div></a>`;
if(pg==='home')(async()=>{
  const P=await posts(),S=await stats(P);
  $('#hero h1').innerHTML=[...SITE].map((c,i)=>`<span style="animation-delay:${i*.07}s">${c}</span>`).join('');
  const draw=q=>{const L=q?P.filter(x=>(x.title+' '+strip(x.body)).toLowerCase().includes(q.toLowerCase())):P;
    $('#list').innerHTML=L.length?'<div class="g">'+L.map((x,i)=>card(x,!i&&!q,S)).join('')+'</div>':'<p class="e">'+(q?'No articles match your search.':'No articles published yet. Check back soon.')+'</p>'};
  draw('');$('#q').oninput=e=>draw(e.target.value.trim());
})();
if(pg==='post')(async()=>{
  const x=(await posts()).find(p=>p.id===new URLSearchParams(location.search).get('id')),m=$('#art');
  if(!x){m.innerHTML='<h1>Post not found</h1><p><a href="./">Back to all articles</a></p>';return}
  document.title=x.title+' | '+SITE;$('meta[name=description]').content=ex(x.body);
  m.innerHTML=`<span class="pill">${esc(x.cat)}</span><h1 dir="auto">${esc(x.title)}</h1><div class="m">${x.date}</div>${x.img?`<img src="${esc(x.img)}" alt="${esc(x.title)}">`:''}<div class="t" dir="auto">${rich(x.body)}</div><p><a href="./">&larr; All articles</a></p>`;social(x,m);
})();
