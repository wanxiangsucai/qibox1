var ImagesSlider=function(){
	this.initialize.apply(this,arguments)
};
ImagesSlider.prototype={
	initialize:function(e,t){
		this.container=document.getElementById(e),
		this.leftHandle=document.getElementById(t.leftHandle||"up"),
		this.rightHandle=document.getElementById(t.rightHandle||"down"),
		this.duration=t.duration||50,
		this.Tween=t.Tween||this.quadEaseOut,
		this.timeLapse=t.timeLapse||10,
		this.variation=t.variation||this.container.parentNode.offsetWidth,
		this.target=0,
		this.maxWidth=this.container.children[0].offsetWidth,
		this.maxIndex=parseInt(this.maxWidth/this.variation),
		this.currentIndex=0;
		var n=this.container.children[0].cloneNode(!0);
		this.container.appendChild(n);
		var r=this;
		this.rightHandle.onclick=function(){r.run(++r.currentIndex,!1)},
		this.leftHandle.onclick=function(){r.run(--r.currentIndex,!0)}
	},
	currentStyle:function(e){
		return e.currentStyle||document.defaultView.getComputedStyle(e,null)
	},
	quadEaseOut:function(e,t,n,r){return-n*(e/=r)*(e-2)+t},
	move:function(){
		var e=this;
		clearTimeout(this.timer),
		this.t<this.duration?(this.boxMoveTo(Math.round(this.Tween(this.t++,this.b,this.c,this.duration))),this.timer=setTimeout(function(){e.move()},this.timeLapse)):this.boxMoveTo(this.target)
	},
	boxMoveTo:function(e){this.container.style.left=e+"px"},
	run:function(e,t){
		e=e<0?this.maxIndex-1:e>this.maxIndex?1:e,
		this.target=-Math.abs(this.variation)*(this.currentIndex=e),
		this.t=0,
		this.b=t?this.target-this.variation:this.target+this.variation,
		this.c=this.target-this.b,
		this.move()
	}
};
new ImagesSlider("ListCompanys",{leftHandle:"atc_up",rightHandle:"atc_down",duration:20,timeLapse:20});