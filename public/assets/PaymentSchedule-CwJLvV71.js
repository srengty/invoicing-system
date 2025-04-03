import{c as V,s as w,a as S,_ as T}from"./AddPayment-xhLs8GyT.js";import{h as Y}from"./moment-C5S46NFB.js";import{B as H,z as D,Y as F,u as L,Z as O,a as y,c as k,d as q,e as $,f as o,h as s,q as U,j as C,g as l,k as p,t as g,i as z,y as b,X as W}from"./app-bSMdDCtv.js";import{c as i,b as X}from"./index-BVmUdKqC.js";import{s as Z}from"./index-BlOd25Hu.js";import{s as R}from"./index-CAdITkwZ.js";import"./index-Ds-6aC4Y.js";import"./index-VTpc7zfg.js";import"./index-t9nxt4Sg.js";import"./index-BRXjObJr.js";import"./index-Dw0KrZvb.js";import"./index-CRC9bhxM.js";var J=H.extend({name:"columngroup"}),Q={name:"BaseColumnGroup",extends:D,props:{type:{type:String,default:null}},style:J,provide:function(){return{$pcColumnGroup:this,$parentInstance:this}}},ee={name:"ColumnGroup",extends:Q,inheritAttrs:!1,inject:["$columnGroups"],mounted:function(){var n;(n=this.$columnGroups)===null||n===void 0||n.add(this.$)},unmounted:function(){var n;(n=this.$columnGroups)===null||n===void 0||n.delete(this.$)},render:function(){return null}},te={name:"Row",extends:D,inject:["$rows"],mounted:function(){var n;(n=this.$rows)===null||n===void 0||n.add(this.$)},unmounted:function(){var n;(n=this.$rows)===null||n===void 0||n.delete(this.$)},render:function(){return null}};const ae={class:"flex items-center gap-2"},fe={__name:"PaymentSchedule",props:F({currency:String,readonly:Boolean,agreement_amount:Number},{modelValue:{default:[]},modelModifiers:{}}),emits:["update:modelValue"],setup(r){const n=L(),m=O(r,"modelValue"),v=r,x=y([]),_=y(4100),G=a=>{let{newData:t,index:e}=a;m.value[e]=t,n.add({severity:"success",summary:"Success",detail:"Payment schedule updated successfully",life:3e3})},B=k(()=>{var a;return((a=V.filter(t=>t.value==v.currency)[0])==null?void 0:a.sign)??"$"}),P=a=>{var t;return`${((t=V.filter(e=>e.value==a.currency)[0])==null?void 0:t.sign)??"$"} ${a.amount.toLocaleString()}`},E=(a,t)=>a=="USD"&&t=="KHR"?1/_.value:a=="KHR"&&t=="USD"?_.value:1,I=k(()=>m.value.reduce((a,t)=>a+E(v.currency,t.currency)*t.amount,0)),d=y(!1),c=y({agreement_amount:200,due_date:new Date,short_description:"Item description",percentage:100,remark:"Additional remark",amount:2e3,currency:"KHR",agreement_currency:"KHR",exchange_rate:4200,status:"Pending"}),h=y(!1),M=async a=>{h.value=!0;try{await new Promise(t=>setTimeout(t,1e3)),n.add({severity:"success",summary:"Invoice Generated",detail:`Invoice for ${a.short_description} has been downloaded`,life:3e3}),console.log("Would generate invoice for:",a)}catch{n.add({severity:"error",summary:"Generation Failed",detail:"Failed to generate invoice. Please try again.",life:3e3})}finally{h.value=!1}},N=a=>{Object.assign(c.value,a),c.value.agreement_currency=a.currency,c.value.agreement_amount=v.agreement_amount??2e3,console.log(v.agreement_amount),d.value=!0},j=a=>{const t=m.value.findIndex(e=>e.id==a.id);t>=0&&(m.value.splice(t,1),n.add({severity:"warn",summary:"Deleted",detail:"Payment schedule removed successfully",life:3e3}))},A=()=>{d.value=!1,n.add({severity:"info",summary:"Cancelled",detail:"Update was cancelled",life:3e3})},K=()=>{d.value=!1;const a=m.value.find(t=>t.id==c.value.id);a?(Object.assign(a,c.value),n.add({severity:"success",summary:"Success",detail:"Payment schedule updated successfully",life:3e3})):n.add({severity:"error",summary:"Error",detail:"Failed to update payment schedule",life:3e3})};return(a,t)=>($(),q("div",null,[o(l(X),{editingRows:x.value,"onUpdate:editingRows":t[0]||(t[0]=e=>x.value=e),editMode:"row",dataKey:"id",onRowEditSave:G,value:m.value,class:"p-datatable-striped",responsiveLayout:"scroll","show-gridlines":!0},{default:s(()=>[o(l(i),{field:"index",header:"No.",sortable:"",class:"text-sm"},{body:s(e=>[p(g(e.index+1),1)]),_:1}),o(l(i),{field:"due_date",header:"Due Date",sortable:"",class:"text-sm"},{body:s(e=>[p(g(l(Y)(e.data.due_date).format("DD/MM/YYYY")),1)]),_:1}),o(l(i),{field:"short_description",header:"Short Description",sortable:"",class:"text-sm"},{editor:s(({data:e,field:u})=>[o(l(Z),{modelValue:e[u],"onUpdate:modelValue":f=>e[u]=f,fluid:"","date-format":"dd/mm/yy"},null,8,["modelValue","onUpdate:modelValue"])]),_:1}),o(l(i),{field:"percentage",header:"Percentage",sortable:"",class:"text-sm"},{body:s(e=>[p(g(e.data.percentage)+" % ",1)]),editor:s(({data:e,field:u})=>[o(l(w),null,{default:s(()=>[o(l(R),{modelValue:e[u],"onUpdate:modelValue":f=>e[u]=f,fluid:""},null,8,["modelValue","onUpdate:modelValue"]),o(l(S),{append:""},{default:s(()=>t[4]||(t[4]=[p("%")])),_:1})]),_:2},1024)]),_:1}),o(l(i),{field:"amount",header:"Amount",style:{"text-align":"right"},sortable:"",class:"text-sm"},{body:s(e=>[p(g(P(e.data)),1)]),editor:s(({data:e,field:u})=>[o(l(w),null,{default:s(()=>[o(l(S),{append:""},{default:s(()=>[p(g(v.currency??"$"),1)]),_:1}),o(l(R),{modelValue:e[u],"onUpdate:modelValue":f=>e[u]=f,fluid:""},null,8,["modelValue","onUpdate:modelValue"])]),_:2},1024)]),_:1}),r.readonly?C("",!0):($(),U(l(i),{key:0,header:"Action",exportable:!1,sortable:"",class:"text-sm"},{body:s(e=>[z("div",ae,[o(l(b),{icon:"pi pi-pencil",class:"p-button-success p-button-outlined",label:"Edit",onClick:u=>N({...e.data})},null,8,["onClick"]),o(l(b),{icon:"pi pi-trash",class:"p-button-danger p-button-outlined",label:"Remove",onClick:u=>j({...e.data})},null,8,["onClick"]),o(l(b),{icon:"pi pi-file-pdf",class:"p-button-success p-button-outlined",label:"Generate invoice",loading:h.value,onClick:u=>M(e.data)},null,8,["loading","onClick"])])]),_:1})),o(l(ee),{type:"footer"},{default:s(()=>[o(l(te),null,{default:s(()=>[o(l(i),{footer:"Total:",footerStyle:"text-align:right",colspan:4,style:{"text-align":"right"}}),o(l(i),{footer:`${B.value} ${I.value.toLocaleString()}`,style:{"text-align":"right"}},null,8,["footer"]),r.readonly?C("",!0):($(),U(l(i),{key:0,footer:"",style:{"text-align":"right"}}))]),_:1})]),_:1})]),_:1},8,["editingRows","value"]),o(l(W),{modelValue:d.value,"onUpdate:modelValue":t[2]||(t[2]=e=>d.value=e),class:"w-1/4",header:"Update payment schedule",visible:d.value,"onUpdate:visible":t[3]||(t[3]=e=>d.value=e),modal:""},{default:s(()=>[o(T,{modelValue:c.value,"onUpdate:modelValue":t[1]||(t[1]=e=>c.value=e),onCancel:A,onSave:K},null,8,["modelValue"])]),_:1},8,["modelValue","visible"])]))}};export{fe as default};
