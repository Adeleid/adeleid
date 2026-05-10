import { cookies } from 'next/headers';
const KEY='admin_session';
export async function isAuthed(){ return (await cookies()).get(KEY)?.value===process.env.ADMIN_SESSION_SECRET; }
export async function setSession(){ (await cookies()).set(KEY,process.env.ADMIN_SESSION_SECRET ?? 'dev-secret',{httpOnly:true,secure:false,path:'/'}); }
