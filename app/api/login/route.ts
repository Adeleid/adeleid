import { prisma } from '@/lib/prisma';
import { setSession } from '@/lib/auth';
import bcrypt from 'bcryptjs';
export async function POST(req:Request){ const {username,password}=await req.json(); const user=await prisma.adminUser.findUnique({where:{username}}); if(!user) return Response.json({ok:false},{status:401}); const ok=await bcrypt.compare(password,user.passwordHash); if(!ok) return Response.json({ok:false},{status:401}); await setSession(); return Response.json({ok:true}); }
