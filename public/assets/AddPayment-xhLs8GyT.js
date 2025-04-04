import{B as h,z as y,d as m,e as g,I as x,H as w,Y as V,u as B,a as S,Z as H,o as R,f as o,i as d,h as r,g as l,k,t as z,y as U}from"./app-bSMdDCtv.js";import{s as G}from"./index-Ds-6aC4Y.js";import{s as K}from"./index-BlOd25Hu.js";import{s as q}from"./index-BRXjObJr.js";import{s as D}from"./index-CAdITkwZ.js";import{s as F}from"./index-Dw0KrZvb.js";var M=({dt:n})=>`
.p-floatlabel {
    display: block;
    position: relative;
}

.p-floatlabel label {
    position: absolute;
    pointer-events: none;
    top: 50%;
    transform: translateY(-50%);
    transition-property: all;
    transition-timing-function: ease;
    line-height: 1;
    font-weight: ${n("floatlabel.font.weight")};
    inset-inline-start: ${n("floatlabel.position.x")};
    color: ${n("floatlabel.color")};
    transition-duration: ${n("floatlabel.transition.duration")};
}

.p-floatlabel:has(.p-textarea) label {
    top: ${n("floatlabel.position.y")};
    transform: translateY(0);
}

.p-floatlabel:has(.p-inputicon:first-child) label {
    inset-inline-start: calc((${n("form.field.padding.x")} * 2) + ${n("icon.size")});
}

.p-floatlabel:has(.p-invalid) label {
    color: ${n("floatlabel.invalid.color")};
}

.p-floatlabel:has(input:focus) label,
.p-floatlabel:has(input.p-filled) label,
.p-floatlabel:has(input:-webkit-autofill) label,
.p-floatlabel:has(textarea:focus) label,
.p-floatlabel:has(textarea.p-filled) label,
.p-floatlabel:has(.p-inputwrapper-focus) label,
.p-floatlabel:has(.p-inputwrapper-filled) label {
    top: ${n("floatlabel.over.active.top")};
    transform: translateY(0);
    font-size: ${n("floatlabel.active.font.size")};
    font-weight: ${n("floatlabel.active.font.weight")};
}

.p-floatlabel:has(input.p-filled) label,
.p-floatlabel:has(textarea.p-filled) label,
.p-floatlabel:has(.p-inputwrapper-filled) label {
    color: ${n("floatlabel.active.color")};
}

.p-floatlabel:has(input:focus) label,
.p-floatlabel:has(input:-webkit-autofill) label,
.p-floatlabel:has(textarea:focus) label,
.p-floatlabel:has(.p-inputwrapper-focus) label {
    color: ${n("floatlabel.focus.color")};
}

.p-floatlabel-in .p-inputtext,
.p-floatlabel-in .p-textarea,
.p-floatlabel-in .p-select-label,
.p-floatlabel-in .p-multiselect-label,
.p-floatlabel-in .p-autocomplete-input-multiple,
.p-floatlabel-in .p-cascadeselect-label,
.p-floatlabel-in .p-treeselect-label {
    padding-block-start: ${n("floatlabel.in.input.padding.top")};
    padding-block-end: ${n("floatlabel.in.input.padding.bottom")};
}

.p-floatlabel-in:has(input:focus) label,
.p-floatlabel-in:has(input.p-filled) label,
.p-floatlabel-in:has(input:-webkit-autofill) label,
.p-floatlabel-in:has(textarea:focus) label,
.p-floatlabel-in:has(textarea.p-filled) label,
.p-floatlabel-in:has(.p-inputwrapper-focus) label,
.p-floatlabel-in:has(.p-inputwrapper-filled) label {
    top: ${n("floatlabel.in.active.top")};
}

.p-floatlabel-on:has(input:focus) label,
.p-floatlabel-on:has(input.p-filled) label,
.p-floatlabel-on:has(input:-webkit-autofill) label,
.p-floatlabel-on:has(textarea:focus) label,
.p-floatlabel-on:has(textarea.p-filled) label,
.p-floatlabel-on:has(.p-inputwrapper-focus) label,
.p-floatlabel-on:has(.p-inputwrapper-filled) label {
    top: 0;
    transform: translateY(-50%);
    border-radius: ${n("floatlabel.on.border.radius")};
    background: ${n("floatlabel.on.active.background")};
    padding: ${n("floatlabel.on.active.padding")};
}
`,P={root:function(f){var u=f.props;return["p-floatlabel",{"p-floatlabel-over":u.variant==="over","p-floatlabel-on":u.variant==="on","p-floatlabel-in":u.variant==="in"}]}},L=h.extend({name:"floatlabel",style:M,classes:P}),Y={name:"BaseFloatLabel",extends:y,props:{variant:{type:String,default:"over"}},style:L,provide:function(){return{$pcFloatLabel:this,$parentInstance:this}}},c={name:"FloatLabel",extends:Y,inheritAttrs:!1};function j(n,f,u,b,e,t){return g(),m("span",w({class:n.cx("root")},n.ptmi("root")),[x(n.$slots,"default")],16)}c.render=j;var N=({dt:n})=>`
.p-inputgroup,
.p-inputgroup .p-iconfield,
.p-inputgroup .p-floatlabel,
.p-inputgroup .p-iftalabel {
    display: flex;
    align-items: stretch;
    width: 100%;
}

.p-inputgroup .p-inputtext,
.p-inputgroup .p-inputwrapper {
    flex: 1 1 auto;
    width: 1%;
}

.p-inputgroupaddon {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: ${n("inputgroup.addon.padding")};
    background: ${n("inputgroup.addon.background")};
    color: ${n("inputgroup.addon.color")};
    border-block-start: 1px solid ${n("inputgroup.addon.border.color")};
    border-block-end: 1px solid ${n("inputgroup.addon.border.color")};
    min-width: ${n("inputgroup.addon.min.width")};
}

.p-inputgroupaddon:first-child,
.p-inputgroupaddon + .p-inputgroupaddon {
    border-inline-start: 1px solid ${n("inputgroup.addon.border.color")};
}

.p-inputgroupaddon:last-child {
    border-inline-end: 1px solid ${n("inputgroup.addon.border.color")};
}

.p-inputgroupaddon:has(.p-button) {
    padding: 0;
    overflow: hidden;
}

.p-inputgroupaddon .p-button {
    border-radius: 0;
}

.p-inputgroup > .p-component,
.p-inputgroup > .p-inputwrapper > .p-component,
.p-inputgroup > .p-iconfield > .p-component,
.p-inputgroup > .p-floatlabel > .p-component,
.p-inputgroup > .p-floatlabel > .p-inputwrapper > .p-component,
.p-inputgroup > .p-iftalabel > .p-component,
.p-inputgroup > .p-iftalabel > .p-inputwrapper > .p-component {
    border-radius: 0;
    margin: 0;
}

.p-inputgroupaddon:first-child,
.p-inputgroup > .p-component:first-child,
.p-inputgroup > .p-inputwrapper:first-child > .p-component,
.p-inputgroup > .p-iconfield:first-child > .p-component,
.p-inputgroup > .p-floatlabel:first-child > .p-component,
.p-inputgroup > .p-floatlabel:first-child > .p-inputwrapper > .p-component,
.p-inputgroup > .p-iftalabel:first-child > .p-component,
.p-inputgroup > .p-iftalabel:first-child > .p-inputwrapper > .p-component {
    border-start-start-radius: ${n("inputgroup.addon.border.radius")};
    border-end-start-radius: ${n("inputgroup.addon.border.radius")};
}

.p-inputgroupaddon:last-child,
.p-inputgroup > .p-component:last-child,
.p-inputgroup > .p-inputwrapper:last-child > .p-component,
.p-inputgroup > .p-iconfield:last-child > .p-component,
.p-inputgroup > .p-floatlabel:last-child > .p-component,
.p-inputgroup > .p-floatlabel:last-child > .p-inputwrapper > .p-component,
.p-inputgroup > .p-iftalabel:last-child > .p-component,
.p-inputgroup > .p-iftalabel:last-child > .p-inputwrapper > .p-component {
    border-start-end-radius: ${n("inputgroup.addon.border.radius")};
    border-end-end-radius: ${n("inputgroup.addon.border.radius")};
}

.p-inputgroup .p-component:focus,
.p-inputgroup .p-component.p-focus,
.p-inputgroup .p-inputwrapper-focus,
.p-inputgroup .p-component:focus ~ label,
.p-inputgroup .p-component.p-focus ~ label,
.p-inputgroup .p-inputwrapper-focus ~ label {
    z-index: 1;
}

.p-inputgroup > .p-button:not(.p-button-icon-only) {
    width: auto;
}

.p-inputgroup .p-iconfield + .p-iconfield .p-inputtext {
    border-inline-start: 0;
}
`,T={root:"p-inputgroup"},E=h.extend({name:"inputgroup",style:N,classes:T}),O={name:"BaseInputGroup",extends:y,style:E,provide:function(){return{$pcInputGroup:this,$parentInstance:this}}},v={name:"InputGroup",extends:O,inheritAttrs:!1};function Z(n,f,u,b,e,t){return g(),m("div",w({class:n.cx("root")},n.ptmi("root")),[x(n.$slots,"default")],16)}v.render=Z;var J={root:"p-inputgroupaddon"},Q=h.extend({name:"inputgroupaddon",classes:J}),W={name:"BaseInputGroupAddon",extends:y,style:Q,provide:function(){return{$pcInputGroupAddon:this,$parentInstance:this}}},$={name:"InputGroupAddon",extends:W,inheritAttrs:!1};function X(n,f,u,b,e,t){return g(),m("div",w({class:n.cx("root")},n.ptmi("root")),[x(n.$slots,"default")],16)}$.render=X;const ee=[{name:"Riels",value:"KHR",sign:"៛"},{name:"USD",value:"USD",sign:"$"}],ne={class:"flex flex-col gap-5 py-2"},ae={class:"flex justify-end gap-2"},ie={__name:"AddPayment",props:V({multiCurrencies:{type:Boolean,default:()=>!1}},{modelValue:{type:Object},modelModifiers:{}}),emits:V(["cancel","save"],["update:modelValue"]),setup(n,{emit:f}){B();const u=S(100),b=f,e=H(n,"modelValue"),t=S({amount:0,percentage:0,currency:"USD"}),i={USDUSD:1,KHRKHR:1,KHRUSD:1/4100,USDKHR:4100};R(()=>{t.value.percentage=(e==null?void 0:e.value.percentage)??0,t.value.currency=(e==null?void 0:e.value.currency)??"USD",i.USDKHR=e.value.exchange_rate??4100,i.KHRUSD=1/i.USDKHR,u.value=(e==null?void 0:e.value.percentage)??100,t.value.amount=(e==null?void 0:e.value.amount)??0,e.value.currency!=e.value.agreement_currency&&(e.value.exchange_rate=i[`${e.value.agreement_currency}${e.value.currency}`],t.value.amount=t.value.amount*e.value.exchange_rate)});const _=s=>{const a=e.value.agreement_amount*i[`${e.value.agreement_currency}${e.value.currency}`];t.value.percentage=Math.round((s.value?s.value:e.value.amount)/a*1e4)/100},I=s=>{const a=e.value.agreement_amount*i[`${e.value.agreement_currency}${e.value.currency}`];t.value.amount=Math.round((s.value?s.value:e.value.percentage)/100*a*100)/100},C=s=>{console.log(`${e.value.agreement_currency}${e.value.currency}`),e.value.currency!=e.value.agreement_currency?(e.value.exchange_rate=i[`${e.value.agreement_currency}${e.value.currency}`],t.value.amount=e.value.agreement_amount*t.value.percentage/100*e.value.exchange_rate):t.value.amount=e.value.agreement_amount*t.value.percentage/100},A=()=>{e.value.amount=t.value.amount,e.value.percentage=t.value.percentage,b("save")};return(s,a)=>(g(),m("div",ne,[o(l(c),{variant:"on"},{default:r(()=>[o(l(G),{id:"total-agreement-amount",modelValue:e.value.agreement_amount,"onUpdate:modelValue":a[0]||(a[0]=p=>e.value.agreement_amount=p),fluid:"",readonly:""},null,8,["modelValue"]),a[7]||(a[7]=d("label",{for:"total-agreement-amount",class:"required"},"Total agreement amount",-1))]),_:1}),o(l(c),{variant:"on"},{default:r(()=>[o(l(K),{id:"due_date",size:"30",modelValue:e.value.due_date,"onUpdate:modelValue":a[1]||(a[1]=p=>e.value.due_date=p),fluid:"","date-format":"dd/mm/yy"},null,8,["modelValue"]),a[8]||(a[8]=d("label",{for:"due_date",class:"required"},"Due date ",-1))]),_:1}),o(l(c),{variant:"on"},{default:r(()=>[o(l(q),{id:"short_description",modelValue:e.value.short_description,"onUpdate:modelValue":a[2]||(a[2]=p=>e.value.short_description=p),fluid:""},null,8,["modelValue"]),a[9]||(a[9]=d("label",{for:"short_description",class:"required"},"Short description",-1))]),_:1}),o(l(c),{variant:"on"},{default:r(()=>[o(l(v),{id:"percentage"},{default:r(()=>[o(l(D),{modelValue:t.value.percentage,"onUpdate:modelValue":a[3]||(a[3]=p=>t.value.percentage=p),fluid:"",onInput:I,min:0,max:u.value,maxFractionDigits:2},null,8,["modelValue","max"]),o(l($),{append:""},{default:r(()=>a[10]||(a[10]=[k("%")])),_:1})]),_:1}),a[11]||(a[11]=d("label",{for:"percentage",class:"required z-10"},"Percentage",-1))]),_:1}),o(l(c),{variant:"on"},{default:r(()=>[o(l(v),{id:"amount"},{default:r(()=>[o(l($),null,{default:r(()=>[k(z(e.value.currency=="USD"?"$":"៛"),1)]),_:1}),o(l(D),{modelValue:t.value.amount,"onUpdate:modelValue":a[4]||(a[4]=p=>t.value.amount=p),fluid:"",onInput:_,maxFractionDigits:2},null,8,["modelValue"])]),_:1}),a[12]||(a[12]=d("label",{for:"amount",class:"required z-10"},"Amount",-1))]),_:1}),o(l(c),{variant:"on"},{default:r(()=>[o(l(F),{id:"currency",modelValue:e.value.currency,"onUpdate:modelValue":a[5]||(a[5]=p=>e.value.currency=p),options:l(ee),optionLabel:"name",optionValue:"value",class:"w-full",onChange:C},null,8,["modelValue","options"]),a[13]||(a[13]=d("label",{for:"currency",class:"required"},"Currency",-1))]),_:1}),d("div",ae,[o(l(U),{label:"Save",class:"grow",onClick:A}),o(l(U),{label:"Cancel",severity:"secondary",onClick:a[6]||(a[6]=p=>b("cancel"))})])]))}};export{ie as _,$ as a,ee as c,v as s};
