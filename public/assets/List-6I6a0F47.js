import{u as oe,a as u,n as le,c as z,C as re,b as ue,d as m,e as c,f as o,i as s,g as l,m as ne,h as i,P as N,j as x,k as d,l as C,t as n,s as F,y as f,p as V,X as L,a2 as U,F as ie,W as w}from"./app-bSMdDCtv.js";import{_ as de}from"./GuestLayout-D6vfYlh4.js";import"./index-CFxwmHLt.js";import{b as O,c as y}from"./index-BVmUdKqC.js";import{s as me}from"./index-Dw0KrZvb.js";import{s as ce}from"./index-DU88kvz-.js";import"./html2pdf-C7KjMRzP.js";import{N as pe}from"./NavbarLayout-B9VpbdtL.js";import{_ as ve}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{s as fe}from"./index-Ds-6aC4Y.js";/* empty css                                                    */import"./index-VTpc7zfg.js";import"./index-CRC9bhxM.js";import"./index-CAdITkwZ.js";import"./index-t9nxt4Sg.js";const be={class:"px-4 py-3 mt-4"},ge={class:"quotations text-sm"},ye={class:"flex justify-end p-4 gap-4"},he=["onClick"],_e={class:"comment-container"},we={key:0,class:"latest-comment"},xe={class:"comment-text"},Ce={key:1,class:"no-comment"},ke={class:"flex gap-4"},Se={key:0,class:"flex flex-col gap-2 text-sm pl-6"},Ae={class:"flex flex-row justify-between mb-6"},Qe={class:"flex flex-col w-1/2 gap-4"},Ne=["href"],Ve={key:1},qe={class:"flex flex-col w-1/2 items-end gap-4"},$e={class:"grid gap-4"},Re={key:0},je={key:1},Ee={key:2},Te={class:"mt-4 p-6"},De={key:0,class:"flex flex-col gap-4"},ze={class:"flex flex-col gap-2"},Fe={class:"flex justify-end gap-2"},Le={__name:"List",props:{customers:Array,products:Array,quotations:Array},setup(I){const q=oe(),_=u(!1),k=u(!1),a=u(null),A=u("manager"),b=u(""),h=u("");u(!1);const Q=u(""),S=u(""),P=u([{label:"Customer Name",value:"customer.name"},{label:"Total",value:"total"},{label:"Status",value:"status"},{label:"Customer Status",value:"customer_status"}]),B=le(),W=z(()=>[{label:"",to:"/",icon:"pi pi-home"},{label:B.props.title||"Quotations",to:route("quotations.list")}]),Y=z(()=>!S.value||!Q.value?$.quotations:$.quotations.filter(r=>{const e=J(r,Q.value);return typeof e=="number"?e.toString().includes(S.value.toLowerCase()):e.toLowerCase().includes(S.value.toLowerCase())})),J=(r,e)=>e.split(".").reduce((v,t)=>v&&v[t],r)||"",p=(r="success",e="Success",v="Operation completed",t=3e3)=>{q.add({severity:r,summary:e,detail:v,life:t,group:"tr"})},$=I;re({id:"",quotation_no:"",quotation_date:"",address:"",phone_number:"",email:"",customer_id:"",total:0,tax:0,products:[]}),u(!1);const M=()=>{a.value.status!=="Approved"?(console.log(a.value),w.visit(route("quotations.create"),{method:"get",data:{quotation:JSON.stringify(a.value)},preserveState:!0,preserveScroll:!0}),_.value=!1):p("error","Edit Disabled","You cannot edit an approved quotation!",3e3)},X=r=>{if(a.value=r,r.comments&&r.comments.length){const e=r.comments[r.comments.length-1];b.value=e.comment,A.value=e.role}else b.value="",A.value="manager";_.value=!0},R=[{field:"quotation_no",header:"Quotation No."},{field:"quotation_date",header:"Quotation Date"},{field:"customer.name",header:"Customer/Organization Name"},{field:"address",header:"Address"},{field:"phone_number",header:"Phone Number"},{field:"email",header:"email"},{field:"terms",header:"Terms"},{field:"total",header:"Total"},{field:"tax",header:"Tax"},{field:"status",header:"Status"},{field:"customer_status",header:"Customer Status"}],G=(r,e=0)=>{const v=`/quotations/${r}?include_catelog=${e}`;window.open(v,"_self")};u(R),u(R),u(),u([{name:"Pending",code:"Pending"},{name:"Approved",code:"Approved"},{name:"Revise",code:"Revise"}]);const j=(r,e)=>{w.put(`/quotations/${r.id}/update-status`,{status:r.status,customer_status:r.customer_status,comment:r.comment,role:r.role},{preserveScroll:!0,onSuccess:v=>{p("success","Success",e,3e3),w.get(route("quotations.list"),{},{replace:!0})},onError:v=>{p("error","Error","Failed to update quotation status!",3e3)}})},H=()=>{if(!b.value.trim()){p("error","Error","Please enter a comment before approving!",3e3);return}a.value.status="Approved",a.value.customer_status="Sent",a.value.comment=b.value,a.value.role=A.value,j(a.value,"Quotation approved and sent to customer!"),_.value=!1,b.value=""},K=()=>{if(!b.value.trim()){p("error","Error","Please enter a comment before revising!",3e3);return}a.value.status="Revise",a.value.comment=b.value,a.value.role=A.value,j(a.value,"Quotation revised and sent to customer!"),_.value=!1,b.value=""},Z=()=>{q.add({severity:"secondary",summary:"Cancelled",detail:"Operation was cancelled.",life:3e3,group:"tr"}),_.value=!1};u({emailChecked:!1,telegramChecked:!1});const ee=async()=>{if(!h.value.trim()){p("error","Error","Please enter a comment before approving!",3e3);return}try{await w.put(`/quotations/${a.value.id}/update-status`,{customer_status:"Accept",status:"Approved",comment:h.value,role:"customer"}),p("success","Success","Quotation approved successfully!",3e3),k.value=!1,h.value="",w.get(route("quotations.list"),{},{preserveScroll:!0})}catch{p("error","Error","Failed to approve quotation.",3e3)}},te=async()=>{if(!h.value.trim()){p("error","Error","Please enter a comment before rejecting!",3e3);return}try{await w.put(`/quotations/${a.value.id}/update-status`,{customer_status:"Reject",status:"Revise",comment:h.value,role:"customer"}),p("success","Success","Quotation rejected successfully!",3e3),k.value=!1,h.value="",w.get(route("quotations.list"),{},{preserveScroll:!0})}catch{p("error","Error","Failed to reject quotation.",3e3)}},se=r=>{a.value=r,r.customer_status==="Sent"?isSendDialogVisible.value=!0:r.customer_status==="Pending"&&(k.value=!0)};return(r,e)=>{const v=ue("tooltip");return c(),m(ie,null,[o(l(ne),{title:"Quotations"}),e[22]||(e[22]=s("meta",{name:"_token",content:"{{ csrf_token() }}"},null,-1)),o(de,null,{default:i(()=>[o(pe),s("div",be,[o(l(ce),{model:W.value,class:"border-none bg-transparent p-0"},{item:i(({item:t})=>[o(l(N),{href:t.to,class:"text-sm hover:text-primary flex items-start justify-center gap-1"},{default:i(()=>[t.icon?(c(),m("i",{key:0,class:C(t.icon)},null,2)):x("",!0),d(" "+n(t.label),1)]),_:2},1032,["href"])]),_:1},8,["model"])]),o(l(F),{position:"top-center",group:"tc"}),o(l(F),{position:"top-right",group:"tr"}),s("div",ge,[s("div",ye,[s("div",null,[o(l(me),{modelValue:Q.value,"onUpdate:modelValue":e[0]||(e[0]=t=>Q.value=t),options:P.value,optionLabel:"label",optionValue:"value",class:"w-48 text-sm",placeholder:"Select field to search"},null,8,["modelValue","options"])]),s("div",null,[o(l(fe),{modelValue:S.value,"onUpdate:modelValue":e[1]||(e[1]=t=>S.value=t),placeholder:"Search",class:"w-64",size:"small"},null,8,["modelValue"])]),s("div",null,[o(l(N),{href:r.route("quotations.create")},{default:i(()=>[o(l(f),{icon:"pi pi-plus",label:"Issue Quotation",size:"small",raised:""})]),_:1},8,["href"])]),o(l(N),{href:r.route("invoices.create")},{default:i(()=>[o(l(f),{icon:"pi pi-plus",label:"Issue Invoice",size:"small",raised:""})]),_:1},8,["href"]),o(l(N),{href:r.route("agreements.create")},{default:i(()=>[o(l(f),{icon:"pi pi-plus",label:"Record Agreement",size:"small",raised:""})]),_:1},8,["href"])]),s("div",null,[o(l(O),{value:Y.value,paginator:"",rows:5,rowsPerPageOptions:[5,10,20,50],tableStyle:"min-width: 50rem"},{default:i(()=>[o(l(y),{field:"customer.name",header:"Customer/Organization Name",style:{width:"15%"}}),o(l(y),{field:"total",header:"Total",style:{width:"10%"}}),o(l(y),{field:"status",header:"Status",style:{width:"10%"}},{body:i(t=>[s("span",{class:C(["p-2 border rounded w-28 h-8 flex items-center justify-center gap-2",{"bg-yellow-100 text-yellow-800 border-yellow-400":t.data.status==="Pending","bg-red-100 text-red-800 border-red-400":t.data.status==="Revise","bg-green-100 text-green-800 border-green-400":t.data.status==="Approved"}])},[s("i",{class:C({"pi pi-clock":t.data.status==="Pending","pi pi-times":t.data.status==="Revise","pi pi-check":t.data.status==="Approved"})},null,2),d(" "+n(t.data.status),1)],2)]),_:1}),o(l(y),{field:"customer_status",header:"Customer Status",style:{width:"10%"}},{body:i(t=>[V((c(),m("span",{onClick:g=>se(t.data),class:C(["p-2 border rounded w-24 h-8 flex items-center justify-center cursor-pointer",{"bg-blue-100 text-blue-800 border-blue-400":t.data.customer_status==="Sent","bg-yellow-100 text-yellow-800 border-yellow-400":t.data.customer_status==="Pending","bg-green-100 text-green-800 border-green-400":t.data.customer_status==="Accept","bg-red-100 text-red-800 border-red-400":t.data.customer_status==="Reject"}])},[s("i",{class:C({"pi pi-send":t.data.customer_status==="Sent","pi pi-clock":t.data.customer_status==="Pending","pi pi-check":t.data.customer_status==="Accept","pi pi-times":t.data.customer_status==="Reject"}),style:{"margin-right":"8px"}},null,2),d(" "+n(t.data.customer_status),1)],10,he)),[[v,"Current customer status: "+t.data.customer_status,void 0,{top:!0}]])]),_:1}),o(l(y),{header:"Customer's Comment",style:{width:"15%","min-width":"200px"},bodyStyle:"white-space: normal"},{body:i(({data:t})=>{var g;return[s("div",_e,[(g=t.comments)!=null&&g.length?(c(),m("div",we,[s("p",xe,n(t.comments[t.comments.length-1].comment),1)])):(c(),m("span",Ce,"No comment"))])]}),_:1}),o(l(y),{header:"View / Print-out",style:{width:"8%"}},{body:i(t=>[s("div",ke,[o(l(f),{icon:"pi pi-eye","aria-label":"View",severity:"info",class:"custom-button",onClick:g=>X(t.data),size:"small",outlined:""},null,8,["onClick"]),V((c(),m("div",null,[o(l(f),{icon:"pi pi-print","aria-label":"Print out",class:"custom-button",onClick:g=>G(t.data.id,1),size:"small",outlined:"",disabled:t.data.status==="Pending"},null,8,["onClick","disabled"])])),[[v,t.data.status==="Pending"?"Printing is disabled for pending quotations":"",void 0,{top:!0}]])])]),_:1})]),_:1},8,["value"]),o(l(L),{visible:_.value,"onUpdate:visible":e[3]||(e[3]=t=>_.value=t),header:"Quotation Details",modal:"",style:{width:"40rem"},class:"text-sm"},{footer:i(()=>[o(l(f),{label:"Approve",class:C({"custom-approved":a.value.status==="Approved"}),severity:"success",onClick:H,disabled:a.value.status==="Approved"},null,8,["class","disabled"]),o(l(f),{label:"Revise",severity:"danger",onClick:K,disabled:a.value.status==="Approved"||a.value.status==="Revise"},null,8,["disabled"]),o(l(f),{label:"Edit",severity:"info",onClick:M,disabled:a.value.status==="Approved"},null,8,["disabled"]),o(l(f),{label:"Close",severity:"secondary",onClick:Z})]),default:i(()=>{var t,g,E,T,D;return[a.value?(c(),m("div",Se,[s("div",Ae,[s("div",Qe,[s("p",null,[e[6]||(e[6]=s("strong",null,"Customer Name:",-1)),d(" "+n(((t=a.value.customer)==null?void 0:t.name)||"N/A"),1)]),s("p",null,[e[7]||(e[7]=s("strong",null,"Address:",-1)),d(" "+n(a.value.address),1)]),s("p",null,[e[8]||(e[8]=s("strong",null,"Phone Number:",-1)),d(" "+n(a.value.phone_number),1)]),s("p",null,[e[9]||(e[9]=s("strong",null,"Email:",-1)),a.value.email||(g=a.value.customer)!=null&&g.email?(c(),m("a",{key:0,href:`mailto:${a.value.email||((E=a.value.customer)==null?void 0:E.email)}`,class:"text-blue-600 hover:underline"},n(a.value.email||((T=a.value.customer)==null?void 0:T.email)),9,Ne)):(c(),m("span",Ve,"N/A"))])]),s("div",qe,[s("div",$e,[s("p",null,[e[10]||(e[10]=s("strong",null,"Quotation No.:",-1)),d(" "+n(a.value.quotation_no),1)]),s("p",null,[e[11]||(e[11]=s("strong",null,"Quotation Date:",-1)),d(" "+n(a.value.quotation_date),1)])])])]),e[15]||(e[15]=s("span",{class:"font-bold block mb-2 text-center"},"Items",-1)),(D=a.value.products)!=null&&D.length?(c(),m("div",Re,[o(l(O),{value:a.value.products,responsiveLayout:"scroll"},{default:i(()=>[o(l(y),{field:"name",header:"Item"}),o(l(y),{field:"pivot.quantity",header:"QTY"}),o(l(y),{field:"pivot.price",header:"Unit Price"})]),_:1},8,["value"])])):x("",!0),e[16]||(e[16]=s("br",null,null,-1)),s("p",null,[e[12]||(e[12]=s("strong",null,"Total:",-1)),d(" "+n(a.value.total),1)]),a.value.comment?(c(),m("p",je,[e[13]||(e[13]=s("strong",null,"Comment:",-1)),d(" "+n(a.value.comment),1)])):x("",!0),a.value.role?(c(),m("p",Ee,[e[14]||(e[14]=s("strong",null,"Role:",-1)),d(" "+n(a.value.role),1)])):x("",!0)])):x("",!0),s("div",Te,[e[17]||(e[17]=s("label",{for:"comment",class:"block font-bold mb-2"},"Comment:",-1)),V(s("textarea",{id:"comment","onUpdate:modelValue":e[2]||(e[2]=ae=>b.value=ae),rows:"3",class:"w-full border rounded p-2",placeholder:"Enter your comment here..."},null,512),[[U,b.value]])])]}),_:1},8,["visible"]),o(l(L),{visible:k.value,"onUpdate:visible":e[5]||(e[5]=t=>k.value=t),header:"Customer Feedback",modal:"",style:{width:"30rem"},class:"text-sm"},{default:i(()=>{var t;return[a.value?(c(),m("div",De,[s("p",null,[e[18]||(e[18]=s("strong",null,"Quotation No.:",-1)),d(" "+n(a.value.quotation_no),1)]),s("p",null,[e[19]||(e[19]=s("strong",null,"Customer Name:",-1)),d(" "+n(((t=a.value.customer)==null?void 0:t.name)||"N/A"),1)]),s("p",null,[e[20]||(e[20]=s("strong",null,"Total:",-1)),d(" "+n(a.value.total),1)]),s("div",ze,[e[21]||(e[21]=s("label",{for:"feedbackComment",class:"block font-bold"},"Comment:",-1)),V(s("textarea",{id:"feedbackComment","onUpdate:modelValue":e[4]||(e[4]=g=>h.value=g),rows:"3",class:"w-full border rounded p-2",placeholder:"Enter your feedback here..."},null,512),[[U,h.value]])]),s("div",Fe,[o(l(f),{label:"Approve",severity:"success",onClick:ee}),o(l(f),{label:"Reject",severity:"danger",onClick:te})])])):x("",!0)]}),_:1},8,["visible"])])])]),_:1})],64)}}},tt=ve(Le,[["__scopeId","data-v-cceced2c"]]);export{tt as default};
