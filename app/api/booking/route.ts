import { prisma } from '@/lib/prisma';
export async function POST(req:Request){const d=await req.json();const r=await prisma.bookingRequest.create({data:d});return Response.json(r)}
export async function GET(){return Response.json(await prisma.bookingRequest.findMany({orderBy:{createdAt:'desc'}}))}
