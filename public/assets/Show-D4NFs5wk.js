import w from"./PaymentSchedule-CwJLvV71.js";import{c as v}from"./AddPayment-xhLs8GyT.js";import{h as V}from"./moment-C5S46NFB.js";import{a as A,r as D,d as b,e as h,f as i,i as e,g as d,m as C,h as c,k as r,t as o,y as f,F as S}from"./app-bSMdDCtv.js";import{a as N}from"./index-Ds-6aC4Y.js";import"./index-BVmUdKqC.js";import"./index-CAdITkwZ.js";import"./index-VTpc7zfg.js";import"./index-CRC9bhxM.js";import"./index-BlOd25Hu.js";import"./index-BRXjObJr.js";import"./index-Dw0KrZvb.js";import"./index-t9nxt4Sg.js";const _={class:"flex flex-col"},B={class:"grid grid-cols-2 md:grid-cols-4 gap-3 print:hidden mb-5 fixed top-6 left-6"},R={class:"flex flex-col items-stretch justify-stretch mx-auto aspect-1/1.414 shadow-lg p-20 min-h-svh border print:border-0 print:shadow-none print:p-0 print:mx-0 print:aspect-none print:w-full print:print-container"},U={class:"grid grid-cols-2"},Y={class:"print:hidden"},$=["href"],H={class:"text-right"},I={class:"print:hidden"},L={__name:"Show",props:{agreement:Object},setup(l){var p,g;const m=l,u=A(4100),y=()=>{window.print()},x=()=>{n.payment_schedules=n.payment_schedules.map(s=>{const t=n.currency=="KHR"&&s.currency=="USD"?u.value:n.currency=="USD"&&s.currency=="KHR"?1/u.value:1;return{...s,currency:n.currency,amount:s.amount*t}})},k=()=>{window.history.back()},n=D({...m.agreement,rate:m.agreement.currency=="KHR"?4100:1,payment_schedules:((g=(p=m.agreement)==null?void 0:p.payment_schedules)==null?void 0:g.map(s=>({...s,due_date:V(s.due_date,"DD/MM/YYYY").toDate()})))??[{id:1,due_date:"28/01/2025",short_description:"Item description",percentage:10,remark:"Additional remark",amount:2e3},{id:2,due_date:"04/02/2025",short_description:"Item description",percentage:20,remark:"Additional remark",amount:5e3},{id:3,due_date:"11/02/2025",short_description:"Item description",percentage:30,remark:"Additional remark",amount:3e3}]});return(s,t)=>(h(),b(S,null,[i(d(C),{title:"Agreement Printing"}),e("div",_,[e("div",B,[i(d(N),{options:d(v),"option-value":"value","option-label":"name",modelValue:n.currency,"onUpdate:modelValue":t[0]||(t[0]=a=>n.currency=a),onValueChange:t[1]||(t[1]=a=>x()),class:"w-full md:w-28 h-10"},null,8,["options","modelValue"]),i(d(f),{onClick:y,class:"md:col-start-2 md:w-28",size:"small"},{default:c(()=>[r("Print "+o(n.currency),1)]),_:1}),i(d(f),{onClick:t[2]||(t[2]=a=>k()),class:"w-full md:w-28 h-10"},{default:c(()=>t[4]||(t[4]=[r("Back to list")])),_:1})]),e("div",R,[t[16]||(t[16]=e("div",{class:"w-full"},null,-1)),t[17]||(t[17]=e("h1",{class:"mx-auto text-2xl"},"Agreement",-1)),e("div",U,[e("div",null,[e("p",null,[t[5]||(t[5]=e("strong",null,"Quotation No:",-1)),r(" "+o(n.quotation_no),1)]),e("p",null,[t[6]||(t[6]=e("strong",null,"Agreement No:",-1)),r(" "+o(n.agreement_no),1)]),e("p",null,[t[7]||(t[7]=e("strong",null,"Customer/Organization:",-1)),r(" "+o(l.agreement.customer.name),1)]),e("p",null,[t[8]||(t[8]=e("strong",null,"Date:",-1)),r(" "+o(l.agreement.agreement_date),1)]),e("p",null,[t[9]||(t[9]=e("strong",null,"Address:",-1)),r(" "+o(n.address),1)]),e("p",Y,[t[10]||(t[10]=e("strong",null,"Agreement Doc:",-1)),e("a",{href:n.agreement_doc,target:"_blank"},"View doc",8,$)])]),e("div",H,[t[15]||(t[15]=e("p",null,[e("strong",null,"Agreement summary")],-1)),e("p",null,[t[11]||(t[11]=e("strong",null,"Start Date:",-1)),r(" "+o(l.agreement.start_date),1)]),e("p",null,[t[12]||(t[12]=e("strong",null,"End Date:",-1)),r(" "+o(l.agreement.end_date),1)]),e("p",null,[t[13]||(t[13]=e("strong",null,"Agreement amount:",-1)),r(" "+o(n.amount_no_tax),1)]),e("p",I,[t[14]||(t[14]=e("strong",null,"Status:",-1)),r(" "+o(n.status),1)])])]),i(w,{class:"mt-2",modelValue:n.payment_schedules,"onUpdate:modelValue":t[3]||(t[3]=a=>n.payment_schedules=a),currency:n.currency,readonly:""},null,8,["modelValue","currency"])])])],64))}};export{L as default};
