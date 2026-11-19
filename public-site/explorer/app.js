(() => {
  'use strict';
  const $=id=>document.getElementById(id), el=$('viewport'), model=HexycleModel;
  const chapters=[
    ['STRUCTURE','装置のしくみ','六角形の骨組みの中に、養液が流れる栽培段を重ねています。部品を選んで観察しましょう。'],
    ['WATER CYCLE','一滴の旅を追う','青い粒が養液です。トレーを外へ進み、下の受け皿で向きを変えて中央へ戻ります。'],
    ['ENVIRONMENT','葉に届く光と風','緑の矢印をたどると、上下段の葉の上から中央に集まった風が、ダクトを昇る風に合流します。各段のファンがこの流れを助けます。']
  ];
  const reduced=matchMedia('(prefers-reduced-motion: reduce)');
  const state={mode:0,part:'frame',stage:0,phase:0,speed:1,playing:false,demo:false,elapsed:0,cue:-1,dark:false,layers:{water:false,light:false,air:false},preset:0,zoom:1};
  const cameras=[[.62,.40,440,2600],[.32,1.49,450,1500],[.67,.54,805,1100],[.60,.22,-453,940],[.42,.70,1365,1340]];
  let theta,pitch,focus,span,renderer,scene,camera,rig,ray,grid,sky,sun,dirty=true;
  const partSelect=$('part');
  for(const [key,[name]] of Object.entries(model.parts))partSelect.add(new Option(name,key));
  const invalidate=()=>{dirty=true};
  function selectPart(k){
    state.part=k;partSelect.value=k;const [name,tag,desc]=model.parts[k];
    $('partTitle').textContent=name;$('status').textContent=tag;$('details').textContent=desc;
    rig?.appearance(k,$('xray').checked,state.layers.light,state.dark);invalidate();
  }
  function preset(i){
    state.preset=i;[theta,pitch,focus,span]=cameras[i];state.zoom=1;
    $('preset').selectedIndex=i;$('viewlabel').textContent=$('preset').options[i].text;
    if(i===3){$('xray').checked=true;selectPart('pump')}invalidate();
  }
  function reflectLayers(){
    for(const k in state.layers){if(rig)rig.effects[k].visible=state.layers[k];if($('layer-'+k))$('layer-'+k).checked=state.layers[k]}
    if(renderer){
      sky.intensity=state.dark?.18:2.3;
      sun.intensity=state.dark?.22:2;
      grid.material.opacity=state.dark?.12:.48;
      el.closest('.stage').classList.toggle('dark-room',state.dark);
      rig.appearance(state.part,$('xray').checked,state.layers.light,state.dark);
    }
    const note=$('illumination');
    if(note)note.textContent=state.dark
      ? (state.layers.light?'点灯中 · 上下各6か所の葉を照らしています（模式表示）':'消灯中 · 育成ライトを点けて葉の明るさを比べよう')
      :'周囲を暗くして、点灯と消灯を見比べられます。';
    invalidate();
  }
  function stage(n,phase=0){
    state.stage=(n+6)%6;state.phase=phase;
    if(state.mode===1){$('step').textContent=(state.stage+1)+' / 6　'+model.stages[state.stage];selectPart(model.stageParts[state.stage])}
    rig?.updateFlow(state.stage,state.phase);invalidate();
  }
  function play(on){state.playing=on;if($('play')){$('play').textContent=on?'Ⅱ 停止':'▷ 再生';$('play').setAttribute('aria-pressed',on)}}
  function stopDemo(){state.demo=false;$('demo').textContent='▷ 自動デモ';$('demo').setAttribute('aria-pressed','false')}
  function mode(n){
    state.mode=n;state.dark=false;play(false);state.layers={water:n===1,light:n===2,air:n===2};
    document.querySelectorAll('[data-mode]').forEach((b,i)=>b.setAttribute('aria-pressed',i===n));
    [$('chapter').textContent,$('heading').textContent,$('intro').textContent]=chapters[n];
    let html=n===1?'<p id="step"></p><div class="flows"><button id="play">▷ 再生</button><button id="next">次の流路 →</button><select id="speed" aria-label="循環の再生速度"><option value=".5">0.5×</option><option value="1">1×</option><option value="2">2×</option></select></div>':'';
    html+='<div>'+Object.entries({water:'水',light:'育成ライト',air:'空気'}).map(([k,label])=>'<label class="check"><input type="checkbox" id="layer-'+k+'">'+label+'</label>').join('')+'</div>';
    if(n===2 && renderer)html+='<div class="lighting-controls"><label class="check"><input type="checkbox" id="dark-room"> 周囲を暗くする</label><p id="illumination" aria-live="polite"></p></div>';
    $('specific').innerHTML=html;
    if($('dark-room'))$('dark-room').onchange=e=>{stopDemo();state.dark=e.target.checked;reflectLayers()};
    for(const k in state.layers)$('layer-'+k).onchange=e=>{stopDemo();state.layers[k]=e.target.checked;if(k==='water'&&!e.target.checked)play(false);reflectLayers()};
    $('xray').checked=n===1;
    if(n===1){
      $('speed').value=state.speed;$('speed').onchange=e=>{state.speed=Number(e.target.value)};
      $('play').onclick=()=>{stopDemo();if(!state.playing){state.layers.water=true;reflectLayers()}play(!state.playing)};
      $('next').onclick=()=>{stopDemo();play(false);stage(state.stage+1)};
      stage(0);
    }else selectPart(n===0?'frame':'led');
    reflectLayers();
    $('annotation').textContent=renderer?['CAD基準の簡略形状 ＋ 仮配置','青色 ＝ 説明用の流路','黄色 ＝ 照射方向　緑の矢印 ＝ 風の合流と上昇'][n]:'CAD 原図表示 · 3D描画を利用できません';
  }
  function fallback(){
    state.dark=false;el.closest('.stage').classList.remove('dark-room');$('dark-room')?.closest('.lighting-controls')?.remove();
    renderer=null;rig=null;el.replaceChildren();const img=document.createElement('img');img.className='fallback';img.src='assets/CAD.png';img.alt='CADに保存されたHexycleの全体図';el.append(img);
    $('annotation').textContent='CAD 原図表示 · 3D描画を利用できません';
    for(const id of ['plus','minus','reset','preset','xray','mature'])$(id).disabled=true;
  }
  try{
    renderer=new THREE.WebGLRenderer({antialias:true,alpha:true,powerPreference:'low-power'});
    renderer.setPixelRatio(Math.min(devicePixelRatio||1,2));el.append(renderer.domElement);
    scene=new THREE.Scene();camera=new THREE.OrthographicCamera(-1,1,1,-1,1,16000);
    sky=new THREE.HemisphereLight(0xfffff4,0xacbba8,2.3);scene.add(sky);
    sun=new THREE.DirectionalLight(0xfff8e6,2);sun.position.set(-900,2200,1100);scene.add(sun);
    grid=new THREE.GridHelper(6000,60,0xa7b9a7,0xc4d2bd);grid.position.y=-675;grid.material.transparent=true;grid.material.opacity=.48;scene.add(grid);
    // Soft contact patch, generated locally without a texture download.
    const pad=document.createElement('canvas');pad.width=pad.height=128;const ctx=pad.getContext('2d'),g=ctx.createRadialGradient(64,64,3,64,64,64);g.addColorStop(0,'rgba(53,74,58,.27)');g.addColorStop(1,'rgba(53,74,58,0)');ctx.fillStyle=g;ctx.fillRect(0,0,128,128);
    const shadow=new THREE.Mesh(new THREE.PlaneGeometry(1550,1550),new THREE.MeshBasicMaterial({map:new THREE.CanvasTexture(pad),transparent:true,depthWrite:false}));shadow.rotation.x=-Math.PI/2;shadow.position.y=-673;scene.add(shadow);
    rig=model.create(THREE,scene);ray=new THREE.Raycaster();
    renderer.domElement.addEventListener('webglcontextlost',e=>{e.preventDefault();fallback()});
  }catch(e){console.warn('3D unavailable',e);fallback()}
  function draw(){
    if(!renderer||!el.clientWidth||!el.clientHeight)return;
    const w=el.clientWidth,h=el.clientHeight,aspect=w/h,half=span/2/state.zoom*Math.max(1,.76/aspect);
    const dpr=renderer.getPixelRatio();
    if(renderer.domElement.width!==Math.floor(w*dpr)||renderer.domElement.height!==Math.floor(h*dpr))renderer.setSize(w,h,false);
    camera.left=-half*aspect;camera.right=half*aspect;camera.top=half;camera.bottom=-half;camera.updateProjectionMatrix();
    camera.position.set(4600*Math.cos(pitch)*Math.sin(theta),focus+4600*Math.sin(pitch),4600*Math.cos(pitch)*Math.cos(theta));camera.lookAt(0,focus,0);renderer.render(scene,camera);
  }
  function zoom(f){state.zoom=Math.max(.5,Math.min(3,state.zoom*f));invalidate()}
  const points=new Map();let dragged=false,travel=0;
  const dist=()=>{const [a,b]=[...points.values()];return Math.hypot(a.x-b.x,a.y-b.y)};
  el.onpointerdown=e=>{if(!renderer)return;stopDemo();el.setPointerCapture(e.pointerId);if(!points.size){dragged=false;travel=0}points.set(e.pointerId,{x:e.clientX,y:e.clientY});if(points.size>1)dragged=true};
  el.onpointermove=e=>{const p=points.get(e.pointerId);if(!p)return;const dx=e.clientX-p.x,dy=e.clientY-p.y,old=points.size===2?dist():0;travel+=Math.hypot(dx,dy);if(travel>4)dragged=true;p.x=e.clientX;p.y=e.clientY;
    if(old>0)zoom(dist()/old);else if(points.size===1){theta-=dx*.009;pitch=Math.max(-.25,Math.min(1.54,pitch+dy*.007));invalidate()}};
  function pick(e){const r=el.getBoundingClientRect();ray.setFromCamera({x:2*(e.clientX-r.left)/r.width-1,y:1-2*(e.clientY-r.top)/r.height},camera);
    const hits=ray.intersectObjects(rig.selectable.filter(m=>m.visible),false);const hit=hits.find(h=>h.object.material.opacity>.2)||hits[0];if(hit)selectPart(hit.object.userData.part)}
  el.onpointerup=e=>{if(points.has(e.pointerId)&&!dragged&&rig)pick(e);points.delete(e.pointerId)};
  el.onlostpointercapture=el.onpointercancel=e=>points.delete(e.pointerId);
  el.addEventListener('wheel',e=>{if(!renderer)return;e.preventDefault();stopDemo();zoom(Math.exp(-e.deltaY*.001))},{passive:false});
  el.onkeydown=e=>{if(!renderer)return;const actions={ArrowLeft:()=>theta-=.14,ArrowRight:()=>theta+=.14,ArrowUp:()=>pitch=Math.min(1.54,pitch+.1),ArrowDown:()=>pitch=Math.max(-.25,pitch-.1),'+':()=>zoom(1.12),'-':()=>zoom(.89),Home:()=>preset(0)};if(actions[e.key]){e.preventDefault();stopDemo();actions[e.key]();invalidate()}};
  $('plus').onclick=()=>{stopDemo();zoom(1.2)};$('minus').onclick=()=>{stopDemo();zoom(1/1.2)};$('reset').onclick=()=>{stopDemo();preset(0)};
  $('mature').onchange=e=>{stopDemo();rig?.setMature(e.target.checked);invalidate()};
  partSelect.onchange=e=>{stopDemo();selectPart(e.target.value)};$('xray').onchange=()=>{stopDemo();selectPart(state.part)};$('preset').onchange=e=>{stopDemo();preset(e.target.selectedIndex)};
  document.querySelectorAll('[data-mode]').forEach(b=>b.onclick=()=>{stopDemo();mode(+b.dataset.mode);preset(0)});
  const dialog=document.querySelector('dialog');$('cad').onclick=()=>{stopDemo();dialog.showModal()};$('close').onclick=()=>dialog.close();
  $('full').onclick=async()=>{try{if(document.fullscreenElement)await document.exitFullscreen();else await document.body.requestFullscreen()}catch{$('annotation').textContent='このブラウザでは全画面表示を利用できません'}};
  document.addEventListener('fullscreenchange',()=>{$('full').textContent=document.fullscreenElement?'⛶ 全画面を終了':'⛶ 全画面';invalidate()});
  // Short repeatable camera sequence. Playback is driven by elapsed visible time.
  const cues=[[0,0,0,'frame'],[8,0,1,'tray'],[16,1,3,'tank'],[20,1,0,'pump'],[44,2,4,'led'],[52,2,0,'fan']];
  function demoAt(t){
    let i=cues.length-1;while(cues[i][0]>t)i--;
    const [start,n,c,k]=cues[i];
    if(i!==state.cue){state.cue=i;mode(n);preset(c);selectPart(k)}
    if(i===3){const f=(t-start)/4,s=Math.min(5,Math.floor(f));if(state.stage!==s||state.part!==model.stageParts[s])stage(s);state.phase=reduced.matches?0:f%1;rig?.updateFlow(s,state.phase)}
    if(!reduced.matches)theta=cameras[c][0]+(t-start)*.028;invalidate();
  }
  $('demo').onclick=()=>{if(state.demo){stopDemo();return}state.demo=true;state.cue=-1;state.elapsed=0;$('demo').textContent='■ デモを終了';$('demo').setAttribute('aria-pressed','true');demoAt(0)};
  let previous=null,windSeconds=0;
  reduced.addEventListener('change',()=>{if(reduced.matches)rig?.updateWind();invalidate()});
  function frame(now){
    const dt=previous===null?0:Math.min(.2,(now-previous)/1000);previous=now;
    if(!document.hidden){
      if(state.demo){state.elapsed+=dt;if(state.elapsed>=60){stopDemo();mode(0);preset(0)}else demoAt(state.elapsed)}
      else if(state.playing){const f=state.phase+dt*state.speed/2.6;if(f>=1)stage(state.stage+1,f%1);else state.phase=f;rig?.updateFlow(state.stage,reduced.matches?0:state.phase);invalidate()}
      if(rig && state.layers.air && !reduced.matches){
        windSeconds=(windSeconds+dt)%2;
        rig.updateWind(windSeconds);invalidate();
      }
      if(dirty){draw();dirty=false}
    }requestAnimationFrame(frame);
  }
  document.addEventListener('visibilitychange',()=>previous=null);new ResizeObserver(invalidate).observe(el);
  mode(0);preset(0);requestAnimationFrame(frame);
})();
