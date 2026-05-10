import { prisma } from '@/lib/prisma';
import { isAuthed } from '@/lib/auth';
export async function GET(){ return Response.json(await prisma.siteContent.findUnique({where:{id:1}})); }
export async function POST(req:Request){ if(!await isAuthed()) return Response.json({error:'unauthorized'},{status:401}); const data=await req.json(); const saved=await prisma.siteContent.update({where:{id:1},data}); return Response.json(saved); }
