const SITE='Iztraab',ADS=''; // after AdSense gives you the code: ADS='ca-pub-1234567890123456'
const $=s=>document.querySelector(s);
const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const strip=h=>h.replace(/<[^>]*>/g,' ').replace(/\s+/g,' ').trim();
const ex=b=>strip(b).slice(0,150)+'...';
const rich=b=>b.includes('<')?b:b.trim().split(/\n{2,}/).map(t=>'<p>'+esc(t).replace(/\n/g,'<br>')+'</p>').join('');
const posts=async()=>{try{return await(await fetch('posts.json?'+Date.now())).json()}catch{return[]}};
if(ADS){const s=document.createElement('script');s.async=true;s.crossOrigin='anonymous';s.src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client='+ADS;document.head.appendChild(s)}
document.body.insertAdjacentHTML('afterbegin','<header class="bar"><a class="logo" href="./">Iztraab</a><nav><a href="./">Home</a><a href="about.html">About</a><a href="contact.html">Contact</a></nav></header>');
document.body.insertAdjacentHTML('beforeend','<footer><b>Iztraab</b><nav><a href="about.html">About</a><a href="contact.html">Contact</a><a href="privacy.html">Privacy Policy</a><a href="terms.html">Terms &amp; Disclaimer</a></nav>&copy; '+new Date().getFullYear()+' Iztraab. All rights reserved.</footer>');
const pg=document.body.dataset.page;
const card=(x,f)=>`<a class="c${f?' f':''}" href="post.html?id=${encodeURIComponent(x.id)}"><div class="im" style="--h:${[...x.id].reduce((a,c)=>a+c.charCodeAt(0)*7,0)%360}${x.img?`;background-image:url('${esc(x.img)}')`:''}"></div><div class="b"><span class="pill">${esc(x.cat)}</span><h2 dir="auto">${esc(x.title)}</h2><p dir="auto">${esc(ex(x.body))}</p><div class="m">${x.date} &middot; ${Math.max(1,Math.round(strip(x.body).split(' ').length/200))} min read</div></div></a>`;
if(pg==='home')(async()=>{
  const P=await posts();
  $('#hero h1').innerHTML=[...SITE].map((c,i)=>`<span style="animation-delay:${i*.07}s">${c}</span>`).join('');
  const draw=q=>{const L=q?P.filter(x=>(x.title+' '+strip(x.body)).toLowerCase().includes(q.toLowerCase())):P;
    $('#list').innerHTML=L.length?'<div class="g">'+L.map((x,i)=>card(x,!i&&!q)).join('')+'</div>':'<p class="e">'+(q?'No articles match your search.':'No articles published yet. Check back soon.')+'</p>'};
  draw('');$('#q').oninput=e=>draw(e.target.value.trim());
})();
if(pg==='post')(async()=>{
  const x=(await posts()).find(p=>p.id===new URLSearchParams(location.search).get('id')),m=$('#art');
  if(!x){m.innerHTML='<h1>Post not found</h1><p><a href="./">Back to all articles</a></p>';return}
  document.title=x.title+' | '+SITE;$('meta[name=description]').content=ex(x.body);
  m.innerHTML=`<span class="pill">${esc(x.cat)}</span><h1 dir="auto">${esc(x.title)}</h1><div class="m">${x.date}</div>${x.img?`<img src="${esc(x.img)}" alt="${esc(x.title)}">`:''}<div class="t" dir="auto">${rich(x.body)}</div><p><a href="./">&larr; All articles</a></p>`;
})();
