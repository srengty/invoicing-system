import{n as b,c as w,u as E,a as v,d as g,e as c,f as t,g as s,m as S,h as a,i as o,P as U,j as $,k as u,l as D,t as y,s as A,y as p,W as P,X as C,F as j}from"./app-DZTdZYTv.js";import{_ as z}from"./GuestLayout-BfdKZXvL.js";import{N as B}from"./NavbarLayout-Bz1XHzsh.js";import{s as M}from"./index-m66pCNzn.js";import{b as F,c as r}from"./index-Dm6_jG8z.js";import{s as f}from"./index-BFVOh3Gg.js";/* empty css                                                    */import"./index-Ch5aU8qY.js";import"./index-DRzkQ_aZ.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./index-B9Lb2ArG.js";import"./index-CHXn2h8A.js";const L={class:"py-3"},T={class:"p-4"},W={class:"flex justify-between items-center ml-4"},I={class:"mt-4 text-sm"},O={class:"grid grid-cols-1 gap-4"},X={class:"grid grid-cols-1 gap-4"},se={__name:"Index",setup(_){const V=b(),N=w(()=>[{label:"",to:"/",icon:"pi pi-home"},{label:V.props.title||"Customer Categories",to:route("customerCategory.index")}]);E();const{customerCategories:k}=b().props,d=v(!1),m=v(!1),i=v({name:"",description:""}),x=()=>{d.value=!0};return(n,e)=>(c(),g(j,null,[t(s(S),{title:"Create Agreement"}),t(z,null,{default:a(()=>[t(B),o("div",L,[t(s(M),{model:N.value,class:"border-none bg-transparent p-0"},{item:a(({item:l})=>[t(s(U),{href:l.to,class:"text-sm hover:text-primary flex items-start justify-center gap-1"},{default:a(()=>[l.icon?(c(),g("i",{key:0,class:D(l.icon)},null,2)):$("",!0),u(" "+y(l.label),1)]),_:2},1032,["href"])]),_:1},8,["model"])]),t(s(A)),o("div",T,[o("div",W,[e[11]||(e[11]=o("h1",{class:"text-xl font-semibold text-gray-800"}," Customer Categories ",-1)),t(s(p),{type:"button",size:"small",class:"p-button-success",onClick:x},{default:a(()=>e[10]||(e[10]=[u("Add New")])),_:1})]),o("div",I,[t(s(F),{value:s(k),paginator:!0,rows:10,rowsPerPageOptions:[5,10,20]},{default:a(()=>[t(s(r),{field:"category_name_khmer",header:"Name"}),t(s(r),{field:"category_name_english",header:"Name En"}),t(s(r),{field:"description_khmer",header:"Description"}),t(s(r),{field:"description_english",header:"Description En"}),t(s(r),{field:"created_at",header:"Created At"},{body:a(l=>[u(y(new Date(l.data.created_at).toLocaleDateString()),1)]),_:1}),t(s(r),{field:"action",header:"Action"},{body:a(l=>[t(s(p),{class:"custom-button",icon:"pi pi-pencil",severity:"warning",size:"small",onClick:q=>s(P).get(n.route("categories.edit",{id:l.data.id})),outlined:""},null,8,["onClick"])]),_:1})]),_:1},8,["value"])]),t(s(C),{modelValue:d.value,"onUpdate:modelValue":e[3]||(e[3]=l=>d.value=l),header:"Add New Customer Category",visible:d.value,"onUpdate:visible":e[4]||(e[4]=l=>d.value=l),modal:""},{default:a(()=>[o("form",{onSubmit:e[2]||(e[2]=(...l)=>n.handleSubmit&&n.handleSubmit(...l))},[o("div",O,[o("div",null,[e[12]||(e[12]=o("label",{for:"name",class:"block text-lg font-medium"},"Name",-1)),t(s(f),{id:"name",modelValue:i.value.name,"onUpdate:modelValue":e[0]||(e[0]=l=>i.value.name=l),class:"w-full",placeholder:"Enter Customer Category Name"},null,8,["modelValue"])]),o("div",null,[e[13]||(e[13]=o("label",{for:"description",class:"block text-lg font-medium"},"Description",-1)),t(s(f),{id:"description",modelValue:i.value.description,"onUpdate:modelValue":e[1]||(e[1]=l=>i.value.description=l),class:"w-full",placeholder:"Enter description"},null,8,["modelValue"])]),o("div",null,[t(s(p),{type:"submit",class:"p-button-success"},{default:a(()=>e[14]||(e[14]=[u("Save")])),_:1})])])],32)]),_:1},8,["modelValue","visible"]),t(s(C),{modelValue:m.value,"onUpdate:modelValue":e[8]||(e[8]=l=>m.value=l),header:"Edit Customer Category",visible:m.value,"onUpdate:visible":e[9]||(e[9]=l=>m.value=l),modal:""},{default:a(()=>[o("form",{onSubmit:e[7]||(e[7]=(...l)=>n.handleEditSubmit&&n.handleEditSubmit(...l))},[o("div",X,[o("div",null,[e[15]||(e[15]=o("label",{for:"name",class:"block text-lg font-medium"},"Name",-1)),t(s(f),{id:"name",modelValue:i.value.name,"onUpdate:modelValue":e[5]||(e[5]=l=>i.value.name=l),class:"w-full",placeholder:"Enter Customer Category Name"},null,8,["modelValue"])]),o("div",null,[e[16]||(e[16]=o("label",{for:"description",class:"block text-lg font-medium"},"Description",-1)),t(s(f),{id:"description",modelValue:i.value.description,"onUpdate:modelValue":e[6]||(e[6]=l=>i.value.description=l),class:"w-full",placeholder:"Enter description"},null,8,["modelValue"])]),o("div",null,[t(s(p),{type:"submit",class:"p-button-success"},{default:a(()=>e[17]||(e[17]=[u("Save")])),_:1})])])],32)]),_:1},8,["modelValue","visible"])])]),_:1})],64))}};export{se as default};
