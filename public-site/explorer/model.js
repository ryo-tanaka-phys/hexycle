/* Model coordinates in mm. Y is vertical. Approximate and teaching-only geometry is documented in README.md. */
window.HexycleModel = (function () {
  const parts = {
    frame:['支持フレーム','CAD寸法・簡略形状','六角形の頂点に立つ6本の支柱と、各段で重なる2つの三角形が栽培部を支えます。支柱中心の半径は350 mm、上下段の間隔は800 mmです。'],
    tray:['栽培トレー','CAD基準・反復は仮配置','養液が根に触れながら内側から外側へ流れます。TR001の床は長さ195 mmに対して外側が4 mm低い設計です。上下各6枚を説明用に配置しています。'],
    catch:['受け皿','CAD基準・簡略形状','トレーの外側から落ちた養液を受け、中央の溝へ集めます。受け面は外側が内側より9.09 mm高く、トレーとは逆向きに傾きます。'],
    tank:['循環タンク','CAD部品・仮外形','装置の下で養液をためます。水に溶けた肥料を、ポンプでもう一度トレーへ送ります。'],
    pump:['ポンプ','CAD部品・仮配置','タンク内の養液を上へ押し上げます。CADにTPF-PUMPがあります。ここでは位置と接続を簡略化しています。'],
    feed:['分配管','模式配管','ポンプが送る養液を上下の段へ運び、各段の6方向へ分ける様子を示します。実際のホースの取り回しは未確認です。'],
    drain:['戻り配管','模式配管','中央に集まった養液をタンクへ戻す経路です。接続口と管の位置は説明用です。'],
    duct:['中央ダクト','設計基準・仮配置','煙突効果でダクト内の空気が下から上へ昇ります。各段の植物の上を通った空気も中央に集まり、この上昇流に合流します。'],
    led:['正六角形の育成ライト','発表用デザイン','上下の植物を、同じ形のライトが1基ずつ照らします。上段用は屋根の下、下段用は交差するパイプの下にあります。黄色の矢印は光の向きです。'],
    canopy:['六角形の屋根','発表用デザイン','淡い緑の半透明パネルを6枚合わせた屋根です。外周のフレームを6本の支柱で支え、上からも内側のライトが見えるようにしています。'],
    fan:['通風ファン','各段1基・形状は模式','上下それぞれのライトと同じ高さにファンがあり、中央ダクトの上向きの流れを助けます。'],
    plant:['作物','説明用','根が薄い養液の流れから水と養分を受け取ります。葉の形や株の位置は栽培のイメージです。']
  };
  const stages=['タンクに養液をためる','ポンプで押し上げる','上下段の6方向へ分ける','トレーを外側へ流れる','受け皿を内側へ戻る','戻り配管からタンクへ'];
  const stageParts=['tank','pump','feed','tray','catch','drain'];
  function create(T, scene) {
    const V=(x,y,z)=>new T.Vector3(x,y,z);
    const polar=(r,y,a)=>V(r*Math.cos(a),y,r*Math.sin(a));
    const groups={}, selectable=[], paths=[], effects={water:new T.Group(),light:new T.Group(),air:new T.Group()};
    const streamSegments=Array.from({length:6},()=>[]);
    Object.keys(parts).forEach(k=>{groups[k]=new T.Group();scene.add(groups[k])});
    Object.values(effects).forEach(g=>scene.add(g));
    const mats=new Map();
    function mesh(group, geo, color, opacity=1) {
      const key=[color,opacity].join('/');
      if(!mats.has(key))mats.set(key,new T.MeshStandardMaterial({color,roughness:.58,metalness:.12,transparent:opacity<1,opacity,depthWrite:opacity===1,side:T.DoubleSide}));
      const m=new T.Mesh(geo,mats.get(key));m.userData.base=m.material;
      if(typeof group==='string'){m.userData.part=group;groups[group].add(m);selectable.push(m)}else group.add(m);
      return m;
    }
    function rod(g,a,b,r,c,op=1,n=12){
      const d=b.clone().sub(a),m=mesh(g,new T.CylinderGeometry(r,r,d.length(),n),c,op);
      m.position.copy(a).lerp(b,.5);m.quaternion.setFromUnitVectors(V(0,1,0),d.normalize());return m;
    }
    function box(g,x,y,z,w,h,l,c,op=1){const m=mesh(g,new T.BoxGeometry(w,h,l),c,op);m.position.set(x,y,z);return m}
    function sheet(g,pts,c,op=1){
      const geo=new T.BufferGeometry();geo.setAttribute('position',new T.Float32BufferAttribute(pts.flatMap(p=>p.toArray()),3));geo.setIndex([0,1,2,0,2,3]);geo.computeVertexNormals();return mesh(g,geo,c,op)
    }
    const steel=0x879e96,joint=0x314f44, pale=0xb9d8bc, moss=0x668d68;
    const ground=-646, step=Math.PI/3, column=(i,y,r=350)=>polar(r,y,(i+.5)*step);
    const plantingY=55,lightBottom=548,matureHeight=(lightBottom-plantingY)*2/3;
    // One stem with staggered, folded leaves. Share the geometry across all twelve plants.
    const matureGeometry=new T.BufferGeometry(),vertices=[],indices=[];
    for(let j=0;j<13;j++){
      const level=j/12, angle=j*2.39996;
      const base=matureHeight*level*.77, rise=matureHeight*(.26-.03*level);
      const reach=77*Math.sin((.18+.78*level)*Math.PI)+18;
      const start=vertices.length/3;
      for(let k=0;k<=8;k++){
        const u=k/8, arch=Math.sin(Math.PI*u);
        const center=polar(reach*u,base+rise*(u+.22*arch),angle+.12*u);
        const edge=polar((18+13*(1-level))*arch,0,angle+Math.PI/2);
        vertices.push(...center.clone().sub(edge).toArray(),...center.clone().add(V(0,6*arch,0)).toArray(),...center.clone().add(edge).toArray());
        if(k>0)for(let side=0;side<2;side++){
          const n=start+(k-1)*3+side;indices.push(n,n+3,n+1,n+1,n+3,n+4);
        }
      }
    }
    matureGeometry.setAttribute('position',new T.Float32BufferAttribute(vertices,3));
    matureGeometry.setIndex(indices);matureGeometry.computeVertexNormals();
    function trimmed(a,b,len){const f=(1-len/a.distanceTo(b))/2;return [a.clone().lerp(b,f),b.clone().lerp(a,f)]}
    for(let i=0;i<6;i++){
      const a=i*step, foot=column(i,ground,519.2), foot2=column(i+1,ground,519.2);
      rod('frame',column(i,-138.53,359.6),column(i,-615.50,509.6),14,steel);
      rod('frame',foot,foot.clone().add(V(0,35,0)),29,joint);
      rod('frame',column(i,-154,364),column(i,-100),22,joint);
      rod('frame',column(i,632),column(i,702),20,joint);
      rod('frame',column(i,1424),column(i,1449),18,joint);
      if([0,1,3,4].includes(i))rod('frame',...trimmed(foot,foot2,500),14,steel);
      rod('frame',...trimmed(column(i,1442),column(i+1,1442),300),14,steel);
      for(const t of [0,800]){
        rod('frame',column(i,t-108),column(i,t+642),14,steel);
        const h=t-(i%2?94:60),p=column(i,h),q=column(i+2,h);
        rod('frame',...trimmed(p,q,530),14,steel);
        rod('frame',column(i,h-36),column(i,h+30),23,joint);
        const ends=trimmed(p,q,530);
        rod('frame',p,ends[0],16,joint);rod('frame',ends[1],q,16,joint);
        const edge=(r,y,side)=>polar(r/Math.cos(step/2),t+y,a+side*step/2);
        const tray=[edge(86.6,43,-1),edge(281.6,39,-1),edge(281.6,39,1),edge(86.6,43,1)];
        sheet('tray',tray,0xaccb9f,.82);
        tray.forEach((p,j)=>{const q=tray[(j+1)%4],up=V(0,18,0);sheet('tray',[p,q,q.clone().add(up),p.clone().add(up)],moss);rod('tray',p.clone().add(up),q.clone().add(up),3,0xd7e9cd)});
        const catchPts=[edge(95.26,0,-1),edge(398.37,9.09,-1),edge(398.37,9.09,1),edge(95.26,0,1)];
        sheet('catch',catchPts,i%2?0x93bda9:0xa2c8b3);
        rod('catch',catchPts[1],catchPts[2],7,0x547e68);
        // A symbolic plant for each growing position.
        const root=polar(205,t+plantingY,a);
        for(let j=0;j<6;j++){
          const theta=j*1.95+a, tip=root.clone().add(polar(46+j*2,42+j*4,theta));
          const mid=root.clone().lerp(tip,.55).add(V(0,15,0)), wing=polar(17,0,theta+Math.PI/2);
          sheet('plant',[root,mid.clone().add(wing),tip,mid.clone().sub(wing)],j%2?0x548e51:0x78ae65);
          rod('plant',root,tip,1.4,0x749f5e,1,5);
        }
        const leaves=mesh('plant',matureGeometry,i%2?0x629c52:0x589348);
        leaves.position.copy(root);leaves.rotation.y=-a;leaves.name='mature-leaves';
        const stem=rod('plant',root,root.clone().add(V(0,matureHeight*.77,0)),3,0x74a25a);
        stem.name='mature-stem';
      }
    }
    // Make one closed six-sided housing and one continuous diffuser per light.
    function hexBand(outer,inner,bottom,top,color){
      const vertices=[],indices=[];
      for(const [r,y] of [[outer,top],[inner,top],[outer,bottom],[inner,bottom]]){
        for(let i=0;i<6;i++)vertices.push(...column(i,y,r).toArray());
      }
      for(let i=0;i<6;i++){
        const j=(i+1)%6;
        for(const [a,b,c,d] of [[i,i+6,j+6,j],[i+12,j+12,j+18,i+18],[i,j,j+12,i+12],[i+6,i+18,j+18,j+6]]){
          indices.push(a,b,c,a,c,d);
        }
      }
      const g=new T.BufferGeometry();g.setAttribute('position',new T.Float32BufferAttribute(vertices,3));g.setIndex(indices);
      const flat=g.toNonIndexed();flat.computeVertexNormals();
      return mesh('led',flat,color);
    }
    // Identical fixtures, spaced at the same 800 mm pitch as the growing tiers.
    for(const bottom of [800+lightBottom,lightBottom]){
      // A broad downlight represents each ring's combined output in the teaching view.
      const growLight=new T.SpotLight(0xfff2c2,4.8,790,.72,.25,0);
      growLight.position.set(0,bottom-6,0);
      growLight.target.position.set(0,bottom-500,0);
      effects.light.add(growLight,growLight.target);
      const fanY=bottom+18;
      for(const side of [-1,1]){
        box('fan',0,fanY,side*56,120,14,8,0x36594d);
        box('fan',side*56,fanY,0,8,14,104,0x36594d);
      }
      rod('fan',V(0,fanY-7,0),V(0,fanY+7,0),11,0x425e51).name='fan-hub';
      for(let blade=0;blade<5;blade++){
        const angle=blade*2*Math.PI/5;
        sheet('fan',[polar(8,fanY,angle),polar(46,fanY+4,angle+.16),
          polar(49,fanY+2,angle+.64),polar(12,fanY-3,angle+.75)],0x86ab95);
      }
      hexBand(312,241,bottom+20,bottom+34,0x305849).name='ring-housing';
      const diffuser=hexBand(307,246,bottom,bottom+20,0xffda77);
      diffuser.name='ring-diffuser';
      diffuser.material.emissive.setHex(0xffc35b);diffuser.material.emissiveIntensity=.55;
      hexBand(304,249,bottom+34,bottom+36,0xffda77).name='ring-top';
      for(let i=0;i<6;i++){
        rod('led',column(i,bottom+92),column(i,bottom+35,279),5,joint);
        effects.light.add(new T.ArrowHelper(V(0,-1,0),polar(242,bottom-10,(i+1)*step),200,0xbc8711,34,20));
      }
    }
    const apex=V(0,1596,0);
    for(let i=0;i<6;i++){
      const rim=column(i,1536,520),next=column(i+1,1536,520),bearing=column(i,1556,350);
      const panel=new T.BufferGeometry();
      panel.setAttribute('position',new T.Float32BufferAttribute([...rim.toArray(),...apex.toArray(),...next.toArray()],3));
      panel.computeVertexNormals();mesh('canopy',panel,i%2?0xb4dccb:0x94ccb6,.20).name='roof-panel';
      rod('canopy',rim,next,8,steel);rod('canopy',rim,apex,3.5,steel);
      rod('canopy',column(i,1444),bearing,8,steel);
    }
    // Tank service envelope and pump positions are illustrative.
    box('tank',0,-463,0,400,348,450,0x507a68);box('tank',0,-280,0,418,20,466,0x83a78e);
    box('pump',-90,-565,50,95,96,75,0x344d52);
    // Open side inlets at leaf height. Their outlines are illustrative.
    for(const low of [137,937]){
      for(let i=0;i<6;i++)rod('duct',column(i,low,100),column(i,low+60,100),2,0x83af99);
    }
    for(const [low,high] of [[12,137],[197,937],[997,1422]]){
      const duct=mesh('duct',new T.CylinderGeometry(100,100,high-low,6,1,true),0x99c3ab,.35);
      duct.position.y=(low+high)/2;
    }
    // Every path is a closed teaching loop for one tier/sector. Segment ownership is explicit.
    const origin=V(10,-505,0), intake=V(-90,-565,50), outlet=V(-90,-485,50);
    const supply=V(-55,-485,0), ret=V(63,-470,0);
    for(const t of [0,800]){
      for(let i=0;i<6;i++){
        const a=i*step, branch=V(-55,t+164,0), inlet=polar(92,t+55,a), outer=polar(278,t+51,a), wet=polar(278,t+7,a), gutter=polar(101,t+2,a), startReturn=V(63,t+2,0);
        const segs=[[origin,intake],[intake,outlet,supply,branch],[branch,polar(92,t+164,a),inlet],[inlet,outer],[outer,wet,gutter],[gutter,startReturn,ret,origin]];
        const route=segs.map((pts,s)=>{
          const curve=new T.CurvePath();for(let k=1;k<pts.length;k++){curve.add(new T.LineCurve3(pts[k-1],pts[k]));
            if(i===0||s===2||s===3||s===4||s===5&&k===1){
              const line=rod(effects.water,pts[k-1],pts[k],5,0x1685b5,.70,8);
              streamSegments[s].push(line);
            }
          }return curve;
        });paths.push(route);
        rod('feed',branch,polar(92,t+164,a),4,0x557f82);
        rod('feed',polar(92,t+164,a),inlet,4,0x557f82);
      }
    }
    rod('feed',outlet,supply,6,0x547b7d);rod('feed',supply,V(-55,964,0),6,0x547b7d);
    rod('drain',V(63,802,0),ret,9,0x637f77);rod('drain',ret,origin,9,0x637f77);
    const dots=paths.map(()=>mesh(effects.water,new T.SphereGeometry(9,9,7),0x168ad1));
    dots.forEach(dot=>{dot.material=new T.MeshBasicMaterial({color:0x0054d0,depthTest:false,transparent:true});dot.scale.setScalar(1.5);dot.renderOrder=4});
    const windColor=0x008b78;
    const windArrows=[];
    const arrowMaterial=new T.MeshBasicMaterial({color:0x00ae92});
    const arrowStem=new T.CylinderGeometry(4.5,4.5,1,10);
    const arrowTip=new T.ConeGeometry(14,24,10);
    function windArrow(point,direction,delay){
      const arrow=new T.Group();
      arrow.name='wind-arrow';
      arrow.position.copy(point);
      arrow.quaternion.setFromUnitVectors(V(0,1,0),direction);
      const stem=new T.Mesh(arrowStem,arrowMaterial),tip=new T.Mesh(arrowTip,arrowMaterial);
      arrow.add(stem,tip);effects.air.add(arrow);
      windArrows.push({stem,tip,delay});
      return arrow;
    }
    function updateWind(seconds=null){
      for(const {stem,tip,delay} of windArrows){
        const phase=seconds===null?1:((seconds/2-delay)%1+1)%1;
        const growth=Math.min(1,phase/.76);
        const length=8+112*growth*growth*(3-2*growth);
        stem.scale.y=length;
        stem.position.y=length/2;
        tip.position.y=length+12;
      }
    }
    // Follow one streamline through the rotor area, clear of the central hubs.
    const airAxis=y=>V(27,y,0);
    rod(effects.air,airAxis(12),airAxis(1500),4.5,windColor).name='updraft';
    for(const y of [24,300,590,820,1100,1356])windArrow(airAxis(y),V(0,1,0),y/2000);
    const foliageAir=[];
    for(const tier of [0,800]){
      const y=tier+162;
      for(let i=0;i<6;i++){
        const a=i*step;
        const path=new T.CubicBezierCurve3(polar(290,y,a),polar(170,y,a),airAxis(y-8),airAxis(y+72));
        const tube=mesh(effects.air,new T.TubeGeometry(path,18,3,7,false),windColor);
        tube.name='leaf-inflow';
        const arrow=windArrow(path.getPointAt(.12),path.getTangentAt(.12),tier/2000);
        const over=tier+plantingY+matureHeight+24;
        const grown=new T.CurvePath();
        grown.add(new T.LineCurve3(polar(290,over,a),polar(121,over,a)));
        grown.add(new T.CubicBezierCurve3(polar(121,over,a),polar(110,over,a),polar(115,y,a),polar(83,y,a)));
        grown.add(new T.CubicBezierCurve3(polar(83,y,a),polar(53,y,a),airAxis(y),airAxis(y+72)));
        foliageAir.push({tube,arrow,seedPath:path,seedTube:tube.geometry,grownPath:grown,grownTube:new T.TubeGeometry(grown,32,3,7,false)});
      }
    }
    updateWind();
    function setMature(enabled){
      groups.plant.children.forEach(m=>{m.visible=m.name.startsWith('mature-')===enabled});
      effects.light.children.filter(m=>m.isSpotLight).forEach(m=>{m.angle=enabled?1.34:.72});
      for(const item of foliageAir){
        const path=enabled?item.grownPath:item.seedPath;
        item.tube.geometry=enabled?item.grownTube:item.seedTube;
        item.arrow.position.copy(path.getPointAt(.12));
        item.arrow.quaternion.setFromUnitVectors(V(0,1,0),path.getTangentAt(.12));
      }
    }
    setMature(false);
    const streamMaterials=[false,true].map(active=>new T.MeshBasicMaterial({color:active?0x007cef:0x43a3cd,transparent:true,opacity:active?1:.36,depthTest:false,depthWrite:false}));
    let lastStage=-1;
    function updateFlow(s,phase){
      if(s!==lastStage){streamSegments.forEach((lines,j)=>lines.forEach(line=>{line.material=streamMaterials[Number(j===s)];line.renderOrder=j===s?3:2}));lastStage=s}
      paths.forEach((p,i)=>dots[i].position.copy(p[s].getPointAt(Math.max(0,Math.min(1,phase)))));
    }
    updateFlow(0,0);
    const highlight=new Map();
    function appearance(id,transparent,lightOn=true,dark=false){
      selectable.forEach(m=>{
        const k=m.userData.part,b=m.userData.base, selected=k===id;
        const fade=transparent&&['tank','tray','catch','duct','plant','frame','canopy'].includes(k)&&(!selected||k==='tank');
        const emitter=m.name==='ring-top'||m.name==='ring-diffuser';
        const key=[b.uuid,selected,fade,emitter,lightOn,dark,k].join('/');
        if(!highlight.has(key)){
          const mat=b.clone();
          if(selected && b.emissive.getHex()===0){mat.emissive.setHex(0x579649);mat.emissiveIntensity=dark?.07:.35}
          if(emitter){
            mat.emissive.setHex(lightOn?0xffda78:0);
            mat.emissiveIntensity=lightOn?1.1:0;
            mat.color.setHex(lightOn?0xffecad:0xb8b9a6);
          }
          if(k==='plant' && dark && lightOn){mat.emissive.copy(b.color);mat.emissiveIntensity=.32}
          if(fade){mat.transparent=true;mat.opacity=.18;mat.depthWrite=false}
          highlight.set(key,mat);
        }
        m.material=highlight.get(key);
      });
    }
    return {groups,selectable,effects,paths,updateFlow,updateWind,setMature,appearance};
  }
  return {parts,stages,stageParts,create};
})();
