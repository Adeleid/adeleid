import { cookies } from 'next/headers';

const KEY = 'admin_session';

function getSessionSecret() {
  return process.env.ADMIN_SESSION_SECRET ?? 'dev-secret';
}

export async function isAuthed() {
  return (await cookies()).get(KEY)?.value === getSessionSecret();
}

export async function setSession() {
  (await cookies()).set(KEY, getSessionSecret(), {
    httpOnly: true,
    sameSite: 'lax',
    secure: process.env.NODE_ENV === 'production',
    path: '/',
    maxAge: 60 * 60 * 8,
  });
}
