import { PrismaClient } from '@prisma/client';
import bcrypt from 'bcryptjs';
const prisma = new PrismaClient();
async function main(){
 await prisma.siteContent.upsert({where:{id:1},update:{},create:{id:1,heroTitle:'جمالك مسؤوليتنا',heroSubtitle:'مع د. سارة الحسيني',whyTitle:'لماذا تختارين د. سارة؟',whyItems:JSON.stringify(['خطة علاج شخصية','نتائج طبيعية وآمنة','متابعة بعد الجلسة']),services:JSON.stringify([{title:'حقن البوتوكس المتوازن',desc:'علاج التجاعيد التعبيرية في الجبهة، حول العين، والرقبة.'},{title:'الفيلر ونحت الملامح',desc:'امتلاء متوازن للشفاه والخدود.'}]),cases:JSON.stringify([{title:'بوتوكس الجبهة وحول العين',before:'/uploads/before1.jpg',after:'/uploads/after1.jpg',result:'اختفاء واضح للتجاعيد'}]),testimonials:JSON.stringify([{name:'م. ندى',text:'النتيجة طبيعية جدًا.'}]),faqs:JSON.stringify([{q:'هل البوتوكس يغيّر ملامحي بالكامل؟',a:'لا، جرعات محسوبة.'}]),contact:JSON.stringify({phone:'+965 99069245',whatsapp:'https://wa.me/96599069245',instagram:'https://www.instagram.com/dr.sara_elhoussiny',address:'الكويت'}),seo:JSON.stringify({title:'د. سارة الحسيني | جلدية وتجميل الجلد',description:'عيادة جلدية وتجميل'})}});
 await prisma.adminUser.upsert({where:{username:'admin'},update:{},create:{username:'admin',passwordHash:await bcrypt.hash('ChangeMe123!',10)}});
}
main().finally(()=>prisma.$disconnect())
