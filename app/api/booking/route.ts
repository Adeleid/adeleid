import { prisma } from '@/lib/prisma';
import { isAuthed } from '@/lib/auth';

export async function POST(req: Request) {
  const d = await req.json();

  const r = await prisma.bookingRequest.create({
    data: {
      name: d.name,
      phone: d.phone,
      service: d.service,
      details: d.details ?? '',
    },
  });

  return Response.json(r);
}

export async function GET() {
  if (!(await isAuthed())) {
    return Response.json({ error: 'unauthorized' }, { status: 401 });
  }

  return Response.json(
    await prisma.bookingRequest.findMany({
      orderBy: { createdAt: 'desc' },
    })
  );
}
