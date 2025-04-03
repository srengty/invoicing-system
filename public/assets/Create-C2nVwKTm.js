import{_ as ne}from"./GuestLayout-D6vfYlh4.js";import{u as oe,n as le,c as x,r as me,a as u,w as de,o as ie,b as ue,d as p,e as c,f as l,g as o,m as ce,h as i,i as s,P as pe,j as h,k as b,l as E,t as g,s as ge,p as U,q as w,v as $,F as R,x as F,y as M,W as B}from"./app-bSMdDCtv.js";import{s as fe}from"./index-C93umJdv.js";import{s as _e}from"./index-BRXjObJr.js";import{h as q}from"./moment-C5S46NFB.js";import{c as ye,s as ve,a as xe}from"./AddPayment-xhLs8GyT.js";import{N as he}from"./NavbarLayout-B9VpbdtL.js";import{s as be}from"./index-DU88kvz-.js";import Ve from"./PaymentSchedule-CwJLvV71.js";import De from"./PopupAddPaymentSchedule-BjjGy7U_.js";import{s as z}from"./index-Ds-6aC4Y.js";import{s as P}from"./index-CAdITkwZ.js";import{s as N}from"./index-BlOd25Hu.js";import{s as T}from"./index-GZbo29i2.js";import{s as L}from"./index-w_r2TDdb.js";import{s as ke}from"./index-eLRo08O_.js";/* empty css                                                    */import"./index-VTpc7zfg.js";import"./index-CRC9bhxM.js";import"./index-Dw0KrZvb.js";import"./index-BVmUdKqC.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./index-t9nxt4Sg.js";const Ae={class:"py-3"},Ue={class:"create-agreement pl-4 pr-4"},we={class:"create-agreement flex flex-row justify-between gap-2"},$e={class:"border border-gray-200 rounded-lg p-4 w-1/2"},qe={class:"grid grid-cols-2 gap-2 items-center"},ze={class:"text-sm"},Se={key:1,class:"pi pi-search text-gray-400"},Re={key:0,class:"pi pi-exclamation-triangle text-yellow-500"},Ne={class:"grid gap-3 w-full"},Ye={class:"text-sm font-medium text-gray-700 mr-5"},Ce=["href"],Ee={class:"border border-gray-200 rounded-lg p-4 w-1/2"},Fe={class:"grid grid-cols-2 gap-2 items-center"},Me={class:"text-sm"},Be={class:"border border-gray-200 rounded-lg p-4 w-1/3"},Pe={class:"space-y-4"},Te={class:"space-y-2"},Le={class:"text-xs font-medium text-gray-700 mr-3"},He={class:"flex items-center gap-2"},Ke=["href"],je=["onClick"],ft={__name:"Create",props:{errors:Object,customers:Array,agreement_max:Number,agreement:Object,edit:Boolean},setup(f){const v=oe(),Y=le(),H=x(()=>[{label:"",to:"/",icon:"pi pi-home"},{label:"Agreements",to:route("agreements.index")},{label:"Create Agreements",to:route("agreements.create")}]),C=a=>a&&decodeURIComponent(a.split(/[\\/]/).pop())||"document.pdf";x(()=>!!t.id);const n=f,_=x({get:()=>t.currency==="KHR",set:a=>{t.currency=a?"KHR":"USD",d.value.exchange_rate=a?4100:1}}),t=me({quotation_no:null,agreement_no:n.edit?n.agreement.agreement_no:Math.max(n.agreement_max,25000001),agreement_date:new Date,customer_id:null,address:"",agreement_doc:[],progress:null,start_date:new Date,end_date:new Date,agreement_amount:0,short_description:"",attachments:[],payment_schedule:[],attachments:null,currency:"KHR"}),V=u("");u("");const K=async()=>{if(t.quotation_no){console.log("Searching for quotation no:",t.quotation_no);try{const a=await axios.get("/search-quotation",{params:{quotation_no:t.quotation_no}});a.data?(V.value=a.data.customer_name,t.address=a.data.address,t.customer_id=a.data.customer_id,t.agreement_amount=a.data.agreement_amount,d.value.agreement_amount=a.data.agreement_amount,a.data.currency==="KHR"?(d.value.exchange_rate=a.data.exchange_rate||4100,_.value=!0):(d.value.exchange_rate=1,_.value=!1)):(V.value="",t.address="",t.customer_id=null,t.agreement_amount=0,d.value.agreement_amount=0,d.value.exchange_rate=1)}catch(a){console.error("Error fetching quotation",a)}}};de(()=>t.quotation_no,a=>{const e=setTimeout(()=>{K()},500);return()=>clearTimeout(e)});const d=u({agreement_amount:t.agreement_amount,due_date:new Date,short_description:"Item description",percentage:100,remark:"Additional remark",amount:2e3,currency:"KHR",agreement_currency:"KHR",exchange_rate:4200});u(1e4),u(),u(""),u(),u(),u("USD"),u("en-US");const k=u([]);u([{name:"US Dollar",code:"USD"},{name:"Euro",code:"EUR"},{name:"British Pound",code:"GBP"}]);const j=x(()=>d.value.agreement_amount-t.payment_schedule.reduce((a,e)=>a+e.amount,0)),I=x(()=>100-t.payment_schedule.reduce((a,e)=>a+e.percentage,0)),S=u(!1);x(()=>t.payment_schedule.some(a=>a.currency!=t.currency)),ie(()=>{var a;if(t.agreement_no=n.edit?n.agreement.agreement_no:Math.max(n.agreement_max,25000001),n.edit){console.log("edit",n.agreement),t.quotation_no=n.agreement.quotation_no,t.agreement_no=n.agreement.agreement_no,t.agreement_reference_no=n.agreement.agreement_reference_no,t.agreement_date=q(n.agreement.agreement_date,"DD/MM/YYYY").toDate(),t.customer_id=n.agreement.customer_id,t.address=n.agreement.address,t.start_date=q(n.agreement.start_date,"DD/MM/YYYY").toDate(),t.end_date=q(n.agreement.end_date,"DD/MM/YYYY").toDate(),t.agreement_amount=n.agreement.amount,t.short_description=n.agreement.short_description,t.currency=n.agreement.currency,n.agreement.agreement_doc&&(k.value=[{path:n.agreement.agreement_doc,name:C(n.agreement.agreement_doc),size:0,type:"application/pdf"}],t.agreement_doc=n.agreement.agreement_doc),t.payment_schedule=(a=n.agreement.payment_schedules)==null?void 0:a.map((m,r)=>({id:m.id,due_date:q(m.due_date,"DD/MM/YYYY").toDate(),short_description:m.short_description,percentage:m.percentage,currency:m.currency,remark:m.remark,amount:m.amount,agreement_currency:n.agreement.currency,exchange_rate:4100,status:m.status}));const e=JSON.parse(n.agreement.attachments??"[]");t.attachments=e==null?void 0:e.map(m=>{const r=typeof m=="string"?m:m.path||m.url;return{path:r,name:C(r),size:m.size||0,type:m.type||"application/pdf"}}),_.value=n.agreement.currency=="KHR",d.value={agreement_amount:n.agreement.amount,due_date:new Date,short_description:"Item description",percentage:100,remark:"Additional remark",amount:2e3,currency:n.agreement.currency,agreement_currency:n.agreement.currency,exchange_rate:4100},ee.value=n.agreement.agreement_doc}});const O=({states:a,valid:e})=>{S.value=!0,t.agreement_amount=d.value.agreement_amount;const m={...t,agreement_date:t.agreement_date?t.agreement_date.toLocaleDateString("fr-FR"):null,start_date:t.start_date?t.start_date.toLocaleDateString("fr-FR"):null,end_date:t.end_date?t.end_date.toLocaleDateString("fr-FR"):null,payment_schedule:t.payment_schedule.map(r=>({...r,due_date:r.due_date?r.due_date.toLocaleDateString("fr-FR"):null}))};console.log("submit",m,e,t.agreement_date),n.edit?(t._method="PUT",B.put(route("agreements.update",n.agreement.agreement_no),m)):B.post(route("agreements.store"),m),S.value=!1},J=a=>{try{const e=JSON.parse(a.xhr.responseText),m=Array.isArray(e)?e:[e];Array.isArray(t.agreement_doc)||(t.agreement_doc=[]),m.forEach(r=>{t.agreement_doc.push(r),k.value.push(r)}),v.add({severity:"success",summary:"Uploaded",detail:`${m.length} agreement doc(s) uploaded successfully`,life:3e3})}catch(e){v.add({severity:"error",summary:"Error",detail:"Failed to upload agreement documents",life:3e3}),console.error("Agreement doc upload error:",e)}},Q=a=>{t.agreement_doc.splice(a,1),k.value.splice(a,1),v.add({severity:"info",summary:"Removed",detail:"Agreement document has been removed",life:3e3})},W=a=>{try{const e=JSON.parse(a.xhr.responseText);Array.isArray(t.attachments)||(t.attachments=[]),t.attachments.push({path:e.path,name:e.name,size:e.size,type:e.mime_type}),v.add({severity:"success",summary:"Uploaded",detail:`Attachment "${e.name}" added successfully`,life:3e3})}catch(e){v.add({severity:"error",summary:"Error",detail:"Failed to upload attachment",life:3e3}),console.error("Attachment upload error:",e)}},G=a=>{t.attachments.splice(a,1),v.add({severity:"info",summary:"Removed",detail:"Attachment has been removed",life:3e3})},X=a=>{var e;a.formData.append("agreement_doc_old",((e=t.agreement_doc)==null?void 0:e.path)||""),a.formData.append("_token",Y.props.csrf_token)},Z=a=>{a.formData.enctype="multipart/form-data",a.formData.append("_token",Y.props.csrf_token)},ee=u(null);u([]);const te=a=>{d.value.currency=a.currency,t.payment_schedule.push({id:t.payment_schedule.length+1,due_date:a.due_date,short_description:a.short_description,percentage:a.percentage,currency:a.currency,remark:a.remark,amount:a.amount,status:a.status??"Pending"})},ae=a=>{d.value.amount=j.value,d.value.percentage=I.value},A=u(!1),re=async()=>{if(!t.agreement_reference_no){A.value=!1;return}try{const a=await axios.get("/api/check-agreement-reference",{params:{reference_no:t.agreement_reference_no}});A.value=a.data.exists}catch(a){console.error("Error checking reference:",a)}};return(a,e)=>{const m=ue("tooltip");return c(),p(R,null,[l(o(ce),{title:"Create Agreements"}),l(ne,null,{default:i(()=>[l(he),s("div",Ae,[l(o(be),{model:H.value,class:"border-none bg-transparent p-0"},{item:i(({item:r})=>[l(o(pe),{href:r.to,class:"text-sm hover:text-primary flex items-start justify-center gap-1"},{default:i(()=>[r.icon?(c(),p("i",{key:0,class:E(r.icon)},null,2)):h("",!0),b(" "+g(r.label),1)]),_:2},1032,["href"])]),_:1},8,["model"])]),s("div",Ue,[l(o(ge)),l(o(fe),{onSubmit:O,action:a.route("agreements.store"),"initial-values":t,class:"mt-6"},{default:i(()=>[s("div",we,[s("div",$e,[s("div",qe,[e[19]||(e[19]=s("span",{class:"col-span-2 text-xl font-semibold mb-5"},"Record Agreement",-1)),U((c(),p("span",ze,e[14]||(e[14]=[b(" Quotation No. ")]))),[[m,"must be approved and no agreement attached"]]),l(o(z),{modelValue:t.quotation_no,"onUpdate:modelValue":e[1]||(e[1]=r=>t.quotation_no=r),placeholder:"Enter Quotation No.",class:"w-full",size:"small"},{suffix:i(()=>[t.quotation_no?(c(),p("i",{key:0,class:"pi pi-times cursor-pointer text-gray-500 hover:text-gray-700",onClick:e[0]||(e[0]=r=>{t.quotation_no="",t.customer_id=null,t.address="",V.value.value=""})})):(c(),p("i",Se))]),_:1},8,["modelValue"]),e[20]||(e[20]=s("span",{class:"text-sm required"},"Agreement No. ",-1)),l(o(P),{modelValue:t.agreement_no,"onUpdate:modelValue":e[2]||(e[2]=r=>t.agreement_no=r),name:"agreement_no",class:E(f.errors.agreement_no?"p-invalid":""),"use-grouping":!1,readonly:!0,size:"small"},null,8,["modelValue","class"]),f.errors.agreement_no?(c(),w(o($),{key:0,severity:"error",variant:"simple",class:"col-span-2",size:"small"},{default:i(()=>[b(g(f.errors.agreement_no),1)]),_:1})):h("",!0),e[21]||(e[21]=s("span",{class:"text-sm"},"Agreement reference No.",-1)),l(o(z),{modelValue:t.agreement_reference_no,"onUpdate:modelValue":e[3]||(e[3]=r=>t.agreement_reference_no=r),placeholder:"Enter reference number",class:"w-full",size:"small",onBlur:re},{suffix:i(()=>[A.value?U((c(),p("i",Re,null,512)),[[m,"This reference number already exists!"]]):h("",!0)]),_:1},8,["modelValue"]),A.value?(c(),w(o($),{key:1,severity:"warn",variant:"simple",class:"col-span-2",size:"small"},{default:i(()=>e[15]||(e[15]=[b("This reference number already exists in our records")])),_:1})):h("",!0),e[22]||(e[22]=s("span",{class:"text-sm required"},"Date",-1)),l(o(N),{"date-format":"dd/mm/yy",name:"agreement_date",modelValue:t.agreement_date,"onUpdate:modelValue":e[4]||(e[4]=r=>t.agreement_date=r),showIcon:"",size:"small"},null,8,["modelValue"]),f.errors.agreement_date?(c(),w(o($),{key:2,severity:"error",variant:"simple",class:"col-span-2",size:"small"},{default:i(()=>[b(g(f.errors.agreement_date),1)]),_:1})):h("",!0),e[23]||(e[23]=s("span",{class:"text-sm required"},"Customer name",-1)),l(o(z),{name:"customer_name",modelValue:V.value,"onUpdate:modelValue":e[5]||(e[5]=r=>V.value=r),size:"small",readonly:""},null,8,["modelValue"]),e[24]||(e[24]=s("span",{class:"text-sm"},"Address",-1)),l(o(z),{name:"address",modelValue:t.address,"onUpdate:modelValue":e[6]||(e[6]=r=>t.address=r),size:"small",readonly:""},null,8,["modelValue"]),e[25]||(e[25]=s("span",{class:"text-sm required"},"Agreement doc",-1)),l(o(T),{name:"agreement_doc",url:a.route("agreements.upload"),mode:"basic",auto:"",multiple:"",accept:"application/pdf",onBeforeUpload:X,onUpload:J,class:"custom-file-upload w-full h-9",chooseLabel:"Upload Agreement PDF"},{chooseicon:i(()=>e[16]||(e[16]=[s("i",{class:"pi pi-file-pdf"},null,-1)])),_:1},8,["url"]),f.errors.agreement_doc?(c(),w(o($),{key:3,severity:"error",size:"small",variant:"simple",class:"col-span-2"},{default:i(()=>[b(g(f.errors.agreement_doc),1)]),_:1})):h("",!0),l(o(L),{value:k.value,class:"col-span-2 mt-2"},{list:i(r=>[s("div",Ne,[(c(!0),p(R,null,F(r.items,(D,y)=>(c(),p("div",{key:y,class:"border border-gray-200 rounded-lg p-3 flex items-center hover:bg-gray-50 transition-colors"},[s("span",Ye," Agreement Doc "+g(y+1)+": ",1),e[17]||(e[17]=s("i",{class:"pi pi-file-pdf mr-2 text-red-500"},null,-1)),s("a",{class:"hover:underline text-sm hover:text-blue-600 flex items-center text-blue-500",href:D.path,target:"_blank"},g(D.name||"document.pdf"),9,Ce),U(l(o(M),{onClick:se=>Q(y),icon:"pi pi-times",text:"",rounded:"",severity:"danger",class:"ml-auto hover:bg-red-50"},null,8,["onClick"]),[[m,"Remove document"]])]))),128))])]),empty:i(()=>e[18]||(e[18]=[s("div",{class:"text-center p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50"},[s("i",{class:"pi pi-inbox text-2xl text-gray-400 mb-2"}),s("p",{class:"text-gray-500"}," No agreement document uploaded ")],-1)])),_:1},8,["value"])])]),s("div",Ee,[s("div",Fe,[e[26]||(e[26]=s("span",{class:"col-span-2 font-semibold text-xl mb-5"},"Agreement summary",-1)),e[27]||(e[27]=s("span",{class:"text-sm"},"Start date",-1)),l(o(N),{"date-format":"dd/mm/yy",name:"start_date",modelValue:t.start_date,"onUpdate:modelValue":e[7]||(e[7]=r=>t.start_date=r),showIcon:"",size:"small"},null,8,["modelValue"]),e[28]||(e[28]=s("span",{class:"text-sm"},"End date",-1)),l(o(N),{"date-format":"dd/mm/yy",name:"end_date",modelValue:t.end_date,"onUpdate:modelValue":e[8]||(e[8]=r=>t.end_date=r),showIcon:"",size:"small"},null,8,["modelValue"]),s("span",Me," Agreement amount ("+g(o(ye)[_.value?1:0].name)+") ",1),l(o(ve),{size:"small"},{default:i(()=>[l(o(xe),null,{default:i(()=>[l(o(ke),{modelValue:_.value,"onUpdate:modelValue":e[9]||(e[9]=r=>_.value=r)},null,8,["modelValue"])]),_:1}),l(o(P),{modelValue:d.value.agreement_amount,"onUpdate:modelValue":e[10]||(e[10]=r=>d.value.agreement_amount=r),value:d.value.agreement_amount,suffix:_.value?"៛":"$"},null,8,["modelValue","value","suffix"])]),_:1}),e[29]||(e[29]=s("span",{class:"text-sm"},"Short description",-1)),l(o(_e),{name:"short_description",rows:"2",modelValue:t.short_description,"onUpdate:modelValue":e[11]||(e[11]=r=>t.short_description=r),size:"small"},null,8,["modelValue"]),e[30]||(e[30]=s("span",{class:"text-sm"},"Payment schedule",-1)),l(De,{modelValue:d.value,"onUpdate:modelValue":e[12]||(e[12]=r=>d.value=r),onSave:te,onUpdate:ae,size:"small",class:"w-full"},null,8,["modelValue"])])]),s("div",Be,[e[36]||(e[36]=s("h3",{class:"font-semibold text-xl mb-4"}," Add Attachment ",-1)),s("div",Pe,[e[35]||(e[35]=s("span",{class:"text-sm required"},"Attachment",-1)),l(o(T),{name:"attachments",url:a.route("agreements.upload"),mode:"basic",auto:"",multiple:"",accept:"application/pdf",onBeforeUpload:Z,onUpload:W,class:"custom-file-upload w-full h-9",chooseLabel:"Upload Attachment(s)"},{chooseicon:i(()=>e[31]||(e[31]=[s("i",{class:"pi pi-paperclip mr-2"},null,-1)])),_:1},8,["url"]),l(o(L),{value:t.attachments,class:"w-full"},{list:i(r=>[s("div",Te,[(c(!0),p(R,null,F(r.items,(D,y)=>(c(),p("div",{key:y,class:"flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"},[s("span",Le," Attachment:"+g(y+1),1),s("div",He,[e[32]||(e[32]=s("i",{class:"pi pi-file-pdf text-red-500"},null,-1)),s("a",{href:D.path,target:"_blank",class:"text-sm font-medium text-blue-500 hover:underline"},g(D.name),9,Ke)]),U((c(),p("button",{onClick:se=>G(y),class:"text-red-500 text-sm hover:text-red-700 p-1 rounded-full hover:bg-red-50"},e[33]||(e[33]=[s("i",{class:"pi pi-times"},null,-1)]),8,je)),[[m,"Remove attachment"]])]))),128))])]),empty:i(()=>e[34]||(e[34]=[s("div",{class:"text-center p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50"},[s("i",{class:"pi pi-inbox text-2xl text-gray-400 mb-2"}),s("p",{class:"text-sm text-gray-500"}," No attachments added ")],-1)])),_:1},8,["value"])])])]),l(Ve,{class:"mt-2",modelValue:t.payment_schedule,"onUpdate:modelValue":e[13]||(e[13]=r=>t.payment_schedule=r),currency:t.currency,agreement_amount:d.value.agreement_amount},null,8,["modelValue","currency","agreement_amount"]),l(o(M),{label:"Save",type:"submit",raised:"",class:"w-48 mt-5",disabled:S.value,icon:"pi pi-check"},null,8,["disabled"])]),_:1},8,["action","initial-values"])])]),_:1})],64)}}};export{ft as default};
