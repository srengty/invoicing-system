import{d as p,e as g,I as v,a as y,w as C,o as D,bk as B,c as S,i as s,f as r,h as c,p as b,at as _,ao as h,l as $,j as E,C as V,k as x,T as U,g as f,av as T}from"./app-DZTdZYTv.js";import{_ as A}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{_ as M,a as N,b as z}from"./TextInput-DfoebpYQ.js";const I={},O={class:"inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:bg-red-700 dark:focus:ring-offset-gray-800"};function P(n,l){return g(),p("button",O,[v(n.$slots,"default")])}const k=A(I,[["render",P]]),W={class:"fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0","scroll-region":""},j={__name:"Modal",props:{show:{type:Boolean,default:!1},maxWidth:{type:String,default:"2xl"},closeable:{type:Boolean,default:!0}},emits:["close"],setup(n,{emit:l}){const t=n,o=l,u=y(),d=y(t.show);C(()=>t.show,()=>{var a;t.show?(document.body.style.overflow="hidden",d.value=!0,(a=u.value)==null||a.showModal()):(document.body.style.overflow="",setTimeout(()=>{var m;(m=u.value)==null||m.close(),d.value=!1},200))});const i=()=>{t.closeable&&o("close")},w=a=>{a.key==="Escape"&&(a.preventDefault(),t.show&&i())};D(()=>document.addEventListener("keydown",w)),B(()=>{document.removeEventListener("keydown",w),document.body.style.overflow=""});const e=S(()=>({sm:"sm:max-w-sm",md:"sm:max-w-md",lg:"sm:max-w-lg",xl:"sm:max-w-xl","2xl":"sm:max-w-2xl"})[t.maxWidth]);return(a,m)=>(g(),p("dialog",{class:"z-50 m-0 min-h-full min-w-full overflow-y-auto bg-transparent backdrop:bg-transparent",ref_key:"dialog",ref:u},[s("div",W,[r(h,{"enter-active-class":"ease-out duration-300","enter-from-class":"opacity-0","enter-to-class":"opacity-100","leave-active-class":"ease-in duration-200","leave-from-class":"opacity-100","leave-to-class":"opacity-0"},{default:c(()=>[b(s("div",{class:"fixed inset-0 transform transition-all",onClick:i},m[0]||(m[0]=[s("div",{class:"absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900"},null,-1)]),512),[[_,n.show]])]),_:1}),r(h,{"enter-active-class":"ease-out duration-300","enter-from-class":"opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95","enter-to-class":"opacity-100 translate-y-0 sm:scale-100","leave-active-class":"ease-in duration-200","leave-from-class":"opacity-100 translate-y-0 sm:scale-100","leave-to-class":"opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"},{default:c(()=>[b(s("div",{class:$(["mb-6 transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:mx-auto sm:w-full dark:bg-gray-800",e.value])},[d.value?v(a.$slots,"default",{key:0}):E("",!0)],2),[[_,n.show]])]),_:3})])],512))}},F=["type"],K={__name:"SecondaryButton",props:{type:{type:String,default:"button"}},setup(n){return(l,t)=>(g(),p("button",{type:n.type,class:"inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"},[v(l.$slots,"default")],8,F))}},L={class:"space-y-6"},q={class:"p-6"},G={class:"mt-6"},H={class:"mt-6 flex justify-end"},X={__name:"DeleteUserForm",setup(n){const l=y(!1),t=y(null),o=V({password:""}),u=()=>{l.value=!0,T(()=>t.value.focus())},d=()=>{o.delete(route("profile.destroy"),{preserveScroll:!0,onSuccess:()=>i(),onError:()=>t.value.focus(),onFinish:()=>o.reset()})},i=()=>{l.value=!1,o.clearErrors(),o.reset()};return(w,e)=>(g(),p("section",L,[e[6]||(e[6]=s("header",null,[s("h2",{class:"text-lg font-medium text-gray-900 dark:text-gray-100"}," Delete Account "),s("p",{class:"mt-1 text-sm text-gray-600 dark:text-gray-400"}," Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain. ")],-1)),r(k,{onClick:u},{default:c(()=>e[1]||(e[1]=[x("Delete Account")])),_:1}),r(j,{show:l.value,onClose:i},{default:c(()=>[s("div",q,[e[4]||(e[4]=s("h2",{class:"text-lg font-medium text-gray-900 dark:text-gray-100"}," Are you sure you want to delete your account? ",-1)),e[5]||(e[5]=s("p",{class:"mt-1 text-sm text-gray-600 dark:text-gray-400"}," Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account. ",-1)),s("div",G,[r(M,{for:"password",value:"Password",class:"sr-only"}),r(N,{id:"password",ref_key:"passwordInput",ref:t,modelValue:f(o).password,"onUpdate:modelValue":e[0]||(e[0]=a=>f(o).password=a),type:"password",class:"mt-1 block w-3/4",placeholder:"Password",onKeyup:U(d,["enter"])},null,8,["modelValue"]),r(z,{message:f(o).errors.password,class:"mt-2"},null,8,["message"])]),s("div",H,[r(K,{onClick:i},{default:c(()=>e[2]||(e[2]=[x(" Cancel ")])),_:1}),r(k,{class:$(["ms-3",{"opacity-25":f(o).processing}]),disabled:f(o).processing,onClick:d},{default:c(()=>e[3]||(e[3]=[x(" Delete Account ")])),_:1},8,["class","disabled"])])])]),_:1},8,["show"])]))}};export{X as default};
