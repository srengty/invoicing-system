const __vite__mapDeps=(i,m=__vite__mapDeps,d=(m.f||(m.f=["assets/auto-DIlnoCct.js","assets/chart-DGFN5v2F.js"])))=>i.map(i=>d[i]);
import{B as _,R as x,L as D,a4 as C,b as R,p as F,d as g,e as b,i as l,I as O,l as M,j as N,H as f,t as h,a7 as w,D as $,G as U,F as V,x as T,q as H,J as q,h as j,z as J,bl as W,o as Y,a as p,f as m,g as S}from"./app-bSMdDCtv.js";import{_ as G}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{a as L}from"./index-Ds-6aC4Y.js";import{s as I}from"./index-t9nxt4Sg.js";import"./index-VTpc7zfg.js";var Q=({dt:t})=>`
.p-togglebutton {
    display: inline-flex;
    cursor: pointer;
    user-select: none;
    overflow: hidden;
    position: relative;
    color: ${t("togglebutton.color")};
    background: ${t("togglebutton.background")};
    border: 1px solid ${t("togglebutton.border.color")};
    padding: ${t("togglebutton.padding")};
    font-size: 1rem;
    font-family: inherit;
    font-feature-settings: inherit;
    transition: background ${t("togglebutton.transition.duration")}, color ${t("togglebutton.transition.duration")}, border-color ${t("togglebutton.transition.duration")},
        outline-color ${t("togglebutton.transition.duration")}, box-shadow ${t("togglebutton.transition.duration")};
    border-radius: ${t("togglebutton.border.radius")};
    outline-color: transparent;
    font-weight: ${t("togglebutton.font.weight")};
}

.p-togglebutton-content {
    display: inline-flex;
    flex: 1 1 auto;
    align-items: center;
    justify-content: center;
    gap: ${t("togglebutton.gap")};
    padding: ${t("togglebutton.content.padding")};
    background: transparent;
    border-radius: ${t("togglebutton.content.border.radius")};
    transition: background ${t("togglebutton.transition.duration")}, color ${t("togglebutton.transition.duration")}, border-color ${t("togglebutton.transition.duration")},
            outline-color ${t("togglebutton.transition.duration")}, box-shadow ${t("togglebutton.transition.duration")};
}

.p-togglebutton:not(:disabled):not(.p-togglebutton-checked):hover {
    background: ${t("togglebutton.hover.background")};
    color: ${t("togglebutton.hover.color")};
}

.p-togglebutton.p-togglebutton-checked {
    background: ${t("togglebutton.checked.background")};
    border-color: ${t("togglebutton.checked.border.color")};
    color: ${t("togglebutton.checked.color")};
}

.p-togglebutton-checked .p-togglebutton-content {
    background: ${t("togglebutton.content.checked.background")};
    box-shadow: ${t("togglebutton.content.checked.shadow")};
}

.p-togglebutton:focus-visible {
    box-shadow: ${t("togglebutton.focus.ring.shadow")};
    outline: ${t("togglebutton.focus.ring.width")} ${t("togglebutton.focus.ring.style")} ${t("togglebutton.focus.ring.color")};
    outline-offset: ${t("togglebutton.focus.ring.offset")};
}

.p-togglebutton.p-invalid {
    border-color: ${t("togglebutton.invalid.border.color")};
}

.p-togglebutton:disabled {
    opacity: 1;
    cursor: default;
    background: ${t("togglebutton.disabled.background")};
    border-color: ${t("togglebutton.disabled.border.color")};
    color: ${t("togglebutton.disabled.color")};
}

.p-togglebutton-label,
.p-togglebutton-icon {
    position: relative;
    transition: none;
}

.p-togglebutton-icon {
    color: ${t("togglebutton.icon.color")};
}

.p-togglebutton:not(:disabled):not(.p-togglebutton-checked):hover .p-togglebutton-icon {
    color: ${t("togglebutton.icon.hover.color")};
}

.p-togglebutton.p-togglebutton-checked .p-togglebutton-icon {
    color: ${t("togglebutton.icon.checked.color")};
}

.p-togglebutton:disabled .p-togglebutton-icon {
    color: ${t("togglebutton.icon.disabled.color")};
}

.p-togglebutton-sm {
    padding: ${t("togglebutton.sm.padding")};
    font-size: ${t("togglebutton.sm.font.size")};
}

.p-togglebutton-sm .p-togglebutton-content {
    padding: ${t("togglebutton.content.sm.padding")};
}

.p-togglebutton-lg {
    padding: ${t("togglebutton.lg.padding")};
    font-size: ${t("togglebutton.lg.font.size")};
}

.p-togglebutton-lg .p-togglebutton-content {
    padding: ${t("togglebutton.content.lg.padding")};
}
`,X={root:function(e){var n=e.instance,a=e.props;return["p-togglebutton p-component",{"p-togglebutton-checked":n.active,"p-invalid":n.$invalid,"p-togglebutton-sm p-inputfield-sm":a.size==="small","p-togglebutton-lg p-inputfield-lg":a.size==="large"}]},content:"p-togglebutton-content",icon:"p-togglebutton-icon",label:"p-togglebutton-label"},Z=_.extend({name:"togglebutton",style:Q,classes:X}),tt={name:"BaseToggleButton",extends:I,props:{onIcon:String,offIcon:String,onLabel:{type:String,default:"Yes"},offLabel:{type:String,default:"No"},iconPos:{type:String,default:"left"},readonly:{type:Boolean,default:!1},tabindex:{type:Number,default:null},ariaLabelledby:{type:String,default:null},ariaLabel:{type:String,default:null},size:{type:String,default:null}},style:Z,provide:function(){return{$pcToggleButton:this,$parentInstance:this}}};function y(t){"@babel/helpers - typeof";return y=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(e){return typeof e}:function(e){return e&&typeof Symbol=="function"&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},y(t)}function et(t,e,n){return(e=nt(e))in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t}function nt(t){var e=ot(t,"string");return y(e)=="symbol"?e:e+""}function ot(t,e){if(y(t)!="object"||!t)return t;var n=t[Symbol.toPrimitive];if(n!==void 0){var a=n.call(t,e);if(y(a)!="object")return a;throw new TypeError("@@toPrimitive must return a primitive value.")}return(e==="string"?String:Number)(t)}var A={name:"ToggleButton",extends:tt,inheritAttrs:!1,emits:["change"],methods:{getPTOptions:function(e){var n=e==="root"?this.ptmi:this.ptm;return n(e,{context:{active:this.active,disabled:this.disabled}})},onChange:function(e){!this.disabled&&!this.readonly&&(this.writeValue(!this.d_value,e),this.$emit("change",e))},onBlur:function(e){var n,a;(n=(a=this.formField).onBlur)===null||n===void 0||n.call(a,e)}},computed:{active:function(){return this.d_value===!0},hasLabel:function(){return C(this.onLabel)&&C(this.offLabel)},label:function(){return this.hasLabel?this.d_value?this.onLabel:this.offLabel:" "},dataP:function(){return D(et({checked:this.active,invalid:this.$invalid},this.size,this.size))}},directives:{ripple:x}},at=["tabindex","disabled","aria-pressed","aria-label","aria-labelledby","data-p-checked","data-p-disabled","data-p"],rt=["data-p"];function lt(t,e,n,a,u,o){var s=R("ripple");return F((b(),g("button",f({type:"button",class:t.cx("root"),tabindex:t.tabindex,disabled:t.disabled,"aria-pressed":t.d_value,onClick:e[0]||(e[0]=function(){return o.onChange&&o.onChange.apply(o,arguments)}),onBlur:e[1]||(e[1]=function(){return o.onBlur&&o.onBlur.apply(o,arguments)})},o.getPTOptions("root"),{"aria-label":t.ariaLabel,"aria-labelledby":t.ariaLabelledby,"data-p-checked":o.active,"data-p-disabled":t.disabled,"data-p":o.dataP}),[l("span",f({class:t.cx("content")},o.getPTOptions("content"),{"data-p":o.dataP}),[O(t.$slots,"default",{},function(){return[O(t.$slots,"icon",{value:t.d_value,class:M(t.cx("icon"))},function(){return[t.onIcon||t.offIcon?(b(),g("span",f({key:0,class:[t.cx("icon"),t.d_value?t.onIcon:t.offIcon]},o.getPTOptions("icon")),null,16)):N("",!0)]}),l("span",f({class:t.cx("label")},o.getPTOptions("label")),h(o.label),17)]})],16,rt)],16,at)),[[s]])}A.render=lt;var it=({dt:t})=>`
.p-selectbutton {
    display: inline-flex;
    user-select: none;
    vertical-align: bottom;
    outline-color: transparent;
    border-radius: ${t("selectbutton.border.radius")};
}

.p-selectbutton .p-togglebutton {
    border-radius: 0;
    border-width: 1px 1px 1px 0;
}

.p-selectbutton .p-togglebutton:focus-visible {
    position: relative;
    z-index: 1;
}

.p-selectbutton .p-togglebutton:first-child {
    border-inline-start-width: 1px;
    border-start-start-radius: ${t("selectbutton.border.radius")};
    border-end-start-radius: ${t("selectbutton.border.radius")};
}

.p-selectbutton .p-togglebutton:last-child {
    border-start-end-radius: ${t("selectbutton.border.radius")};
    border-end-end-radius: ${t("selectbutton.border.radius")};
}

.p-selectbutton.p-invalid {
    outline: 1px solid ${t("selectbutton.invalid.border.color")};
    outline-offset: 0;
}
`,st={root:function(e){var n=e.instance;return["p-selectbutton p-component",{"p-invalid":n.$invalid}]}},ut=_.extend({name:"selectbutton",style:it,classes:st}),dt={name:"BaseSelectButton",extends:I,props:{options:Array,optionLabel:null,optionValue:null,optionDisabled:null,multiple:Boolean,allowEmpty:{type:Boolean,default:!0},dataKey:null,ariaLabelledby:{type:String,default:null},size:{type:String,default:null}},style:ut,provide:function(){return{$pcSelectButton:this,$parentInstance:this}}};function ct(t,e){var n=typeof Symbol<"u"&&t[Symbol.iterator]||t["@@iterator"];if(!n){if(Array.isArray(t)||(n=E(t))||e){n&&(t=n);var a=0,u=function(){};return{s:u,n:function(){return a>=t.length?{done:!0}:{done:!1,value:t[a++]}},e:function(i){throw i},f:u}}throw new TypeError(`Invalid attempt to iterate non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}var o,s=!0,r=!1;return{s:function(){n=n.call(t)},n:function(){var i=n.next();return s=i.done,i},e:function(i){r=!0,o=i},f:function(){try{s||n.return==null||n.return()}finally{if(r)throw o}}}}function bt(t){return ft(t)||pt(t)||E(t)||gt()}function gt(){throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function E(t,e){if(t){if(typeof t=="string")return k(t,e);var n={}.toString.call(t).slice(8,-1);return n==="Object"&&t.constructor&&(n=t.constructor.name),n==="Map"||n==="Set"?Array.from(t):n==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?k(t,e):void 0}}function pt(t){if(typeof Symbol<"u"&&t[Symbol.iterator]!=null||t["@@iterator"]!=null)return Array.from(t)}function ft(t){if(Array.isArray(t))return k(t)}function k(t,e){(e==null||e>t.length)&&(e=t.length);for(var n=0,a=Array(e);n<e;n++)a[n]=t[n];return a}var z={name:"SelectButton",extends:dt,inheritAttrs:!1,emits:["change"],methods:{getOptionLabel:function(e){return this.optionLabel?$(e,this.optionLabel):e},getOptionValue:function(e){return this.optionValue?$(e,this.optionValue):e},getOptionRenderKey:function(e){return this.dataKey?$(e,this.dataKey):this.getOptionLabel(e)},isOptionDisabled:function(e){return this.optionDisabled?$(e,this.optionDisabled):!1},isOptionReadonly:function(e){if(this.allowEmpty)return!1;var n=this.isSelected(e);return this.multiple?n&&this.d_value.length===1:n},onOptionSelect:function(e,n,a){var u=this;if(!(this.disabled||this.isOptionDisabled(n)||this.isOptionReadonly(n))){var o=this.isSelected(n),s=this.getOptionValue(n),r;if(this.multiple)if(o){if(r=this.d_value.filter(function(d){return!w(d,s,u.equalityKey)}),!this.allowEmpty&&r.length===0)return}else r=this.d_value?[].concat(bt(this.d_value),[s]):[s];else{if(o&&!this.allowEmpty)return;r=o?null:s}this.writeValue(r,e),this.$emit("change",{event:e,value:r})}},isSelected:function(e){var n=!1,a=this.getOptionValue(e);if(this.multiple){if(this.d_value){var u=ct(this.d_value),o;try{for(u.s();!(o=u.n()).done;){var s=o.value;if(w(s,a,this.equalityKey)){n=!0;break}}}catch(r){u.e(r)}finally{u.f()}}}else n=w(this.d_value,a,this.equalityKey);return n}},computed:{equalityKey:function(){return this.optionValue?null:this.dataKey},dataP:function(){return D({invalid:this.$invalid})}},directives:{ripple:x},components:{ToggleButton:A}},ht=["aria-labelledby","data-p"];function mt(t,e,n,a,u,o){var s=U("ToggleButton");return b(),g("div",f({class:t.cx("root"),role:"group","aria-labelledby":t.ariaLabelledby},t.ptmi("root"),{"data-p":o.dataP}),[(b(!0),g(V,null,T(t.options,function(r,d){return b(),H(s,{key:o.getOptionRenderKey(r),modelValue:o.isSelected(r),onLabel:o.getOptionLabel(r),offLabel:o.getOptionLabel(r),disabled:t.disabled||o.isOptionDisabled(r),unstyled:t.unstyled,size:t.size,readonly:o.isOptionReadonly(r),onChange:function(c){return o.onOptionSelect(c,r,d)},pt:t.ptm("pcToggleButton")},q({_:2},[t.$slots.option?{name:"default",fn:j(function(){return[O(t.$slots,"option",{option:r,index:d},function(){return[l("span",f({ref_for:!0},t.ptm("pcToggleButton").label),h(o.getOptionLabel(r)),17)]})]}),key:"0"}:void 0]),1032,["modelValue","onLabel","offLabel","disabled","unstyled","size","readonly","onChange","pt"])}),128))],16,ht)}z.render=mt;var yt={root:{position:"relative"}},vt={root:"p-chart"},$t=_.extend({name:"chart",classes:vt,inlineStyles:yt}),St={name:"BaseChart",extends:J,props:{type:String,data:null,options:null,plugins:null,width:{type:Number,default:300},height:{type:Number,default:150},canvasProps:{type:null,default:null}},style:$t,provide:function(){return{$pcChart:this,$parentInstance:this}}},K={name:"Chart",extends:St,inheritAttrs:!1,emits:["select","loaded"],chart:null,watch:{data:{handler:function(){this.reinit()},deep:!0},type:function(){this.reinit()},options:function(){this.reinit()}},mounted:function(){this.initChart()},beforeUnmount:function(){this.chart&&(this.chart.destroy(),this.chart=null)},methods:{initChart:function(){var e=this;W(()=>import("./auto-DIlnoCct.js"),__vite__mapDeps([0,1])).then(function(n){e.chart&&(e.chart.destroy(),e.chart=null),n&&n.default&&(e.chart=new n.default(e.$refs.canvas,{type:e.type,data:e.data,options:e.options,plugins:e.plugins})),e.$emit("loaded",e.chart)})},getCanvas:function(){return this.$canvas},getChart:function(){return this.chart},getBase64Image:function(){return this.chart.toBase64Image()},refresh:function(){this.chart&&this.chart.update()},reinit:function(){this.initChart()},onCanvasClick:function(e){if(this.chart){var n=this.chart.getElementsAtEventForMode(e,"nearest",{intersect:!0},!1),a=this.chart.getElementsAtEventForMode(e,"dataset",{intersect:!0},!1);n&&n[0]&&a&&this.$emit("select",{originalEvent:e,element:n[0],dataset:a})}},generateLegend:function(){if(this.chart)return this.chart.generateLegend()}}};function v(t){"@babel/helpers - typeof";return v=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(e){return typeof e}:function(e){return e&&typeof Symbol=="function"&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},v(t)}function P(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(t);e&&(a=a.filter(function(u){return Object.getOwnPropertyDescriptor(t,u).enumerable})),n.push.apply(n,a)}return n}function B(t){for(var e=1;e<arguments.length;e++){var n=arguments[e]!=null?arguments[e]:{};e%2?P(Object(n),!0).forEach(function(a){wt(t,a,n[a])}):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):P(Object(n)).forEach(function(a){Object.defineProperty(t,a,Object.getOwnPropertyDescriptor(n,a))})}return t}function wt(t,e,n){return(e=Ot(e))in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t}function Ot(t){var e=kt(t,"string");return v(e)=="symbol"?e:e+""}function kt(t,e){if(v(t)!="object"||!t)return t;var n=t[Symbol.toPrimitive];if(n!==void 0){var a=n.call(t,e);if(v(a)!="object")return a;throw new TypeError("@@toPrimitive must return a primitive value.")}return(e==="string"?String:Number)(t)}var _t=["width","height"];function Ct(t,e,n,a,u,o){return b(),g("div",f({class:t.cx("root"),style:t.sx("root")},t.ptmi("root")),[l("canvas",f({ref:"canvas",width:t.width,height:t.height,onClick:e[0]||(e[0]=function(s){return o.onCanvasClick(s)})},B(B({},t.canvasProps),t.ptm("canvas"))),null,16,_t)],16)}K.render=Ct;const Lt={class:"shadow-md"},Pt={__name:"StackedBar",setup(t){Y(()=>{e.value=a(),n.value=u()});const e=p(),n=p(),a=()=>({labels:["January","February","March","April","May","June","July"],datasets:[{type:"bar",label:"Quoations",backgroundColor:"#10b981",data:[50,25,12,48,90,76,42]},{type:"bar",label:"Agreements",backgroundColor:"#00a38c",data:[21,84,24,75,37,65,34]},{type:"bar",label:"Ìnvoices",backgroundColor:"#008c8d",data:[41,52,24,74,23,21,32]}]}),u=()=>{const o=getComputedStyle(document.documentElement),s=o.getPropertyValue("--p-text-color"),r=o.getPropertyValue("--p-text-muted-color"),d=o.getPropertyValue("--p-content-border-color");return{maintainAspectRatio:!1,aspectRatio:.8,plugins:{tooltips:{mode:"index",intersect:!1},legend:{labels:{color:s}}},scales:{x:{stacked:!0,ticks:{color:r},grid:{color:d}},y:{stacked:!0,ticks:{color:r},grid:{color:d}}}}};return(o,s)=>(b(),g("div",Lt,[m(S(K),{type:"bar",data:e.value,options:n.value,class:"h-[30rem]"},null,8,["data","options"])]))}},Bt={class:"surface-ground"},xt={class:"grid"},Dt={class:"flex justify-between items-center"},Vt={class:"flex gap-2"},Tt={key:0,class:"flex item-center w-full md:w-44 text-sm"},jt={class:"text-sm"},It={key:1},At={class:"flex gap-6 w-full mt-6"},Et={class:"col-12 w-1/3 shadow-md"},zt={class:"p-4"},Kt={class:"grid gap-6 mb-4"},Rt={class:"text-600 font-medium"},Ft={class:"text-900 font-semibold"},Mt={class:"w-2/3"},Nt={__name:"DashboardBarChart",setup(t){const e=p([{label:"Invoices",value:"0.00"},{label:"Payments",value:"0.00"},{label:"Expenses",value:"0.00"},{label:"Outstanding",value:"0.00"}]),n=p({name:"Day",value:"Day"}),a=p([{name:"Day",value:"Day"},{name:"Week",value:"Week"},{name:"Month",value:"Month"}]),u=p({name:"KHR",code:"KHR"}),o=p([{name:"USD",code:"USD"},{name:"KHR",code:"KHR"}]),s=p(),r=p([{label:"Last 7 Days",value:"7days",count:6},{label:"Last 30 Days",value:"30days"},{label:"This Month",value:"month"},{label:"Last Month",value:"lastmonth"},{label:"This Year",value:"year"},{label:"Last Year",value:"lastyear"}]);return(d,i)=>(b(),g("div",Bt,[l("div",xt,[l("div",Dt,[i[4]||(i[4]=l("div",null,[l("h1",{class:"text-md font-medium text-gray-400 mb-5"}," Welcome! Dashboard to see you... ")],-1)),l("div",Vt,[m(S(L),{modelValue:u.value,"onUpdate:modelValue":i[0]||(i[0]=c=>u.value=c),options:o.value,optionLabel:"name",placeholder:"KHR/USD",class:"w-full md:w-30 h-9",size:"small"},null,8,["modelValue","options"]),m(S(z),{modelValue:n.value,"onUpdate:modelValue":i[1]||(i[1]=c=>n.value=c),options:a.value,optionLabel:"name","aria-labelledby":"period-label",class:"w-full md:w-30 h-9",size:"small"},null,8,["modelValue","options"]),m(S(L),{modelValue:s.value,"onUpdate:modelValue":i[2]||(i[2]=c=>s.value=c),options:r.value,optionLabel:"label",placeholder:"Select time range",class:"w-full md:w-44 h-9",size:"small"},{value:j(c=>[c.value?(b(),g("div",Tt,[i[3]||(i[3]=l("div",null,[l("i",{class:"pi pi-calendar mr-2 text-sm"})],-1)),l("div",jt,h(c.value.label),1)])):(b(),g("span",It,h(c.placeholder),1))]),_:1},8,["modelValue","options"])])]),l("div",At,[l("div",Et,[i[6]||(i[6]=l("div",{class:"text-lg font-semibold border-b-4 p-4"}," Recent Transactions ",-1)),l("div",zt,[l("div",Kt,[(b(!0),g(V,null,T(e.value,c=>(b(),g("div",{key:c.label,class:"flex justify-between text-gray-500 align-items-center p-2 border-bottom-1 surface-border border-b-2"},[l("span",Rt,h(c.label),1),l("span",Ft,"៛ "+h(c.value),1)]))),128))]),i[5]||(i[5]=l("div",{class:"flex justify-between align-items-center p-3 surface-100 border-round"},[l("span",{class:"text-600 font-medium"},"Total Invoices Outstanding"),l("span",{class:"text-900 font-bold"},"៛0")],-1))])]),l("div",Mt,[m(Pt)])])])]))}},Yt=G(Nt,[["__scopeId","data-v-5fe71714"]]);export{Yt as default};
