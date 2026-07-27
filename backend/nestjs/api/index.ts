import { NestFactory } from '@nestjs/core';
import { NestExpressApplication } from '@nestjs/platform-express';
import { AppModule } from '../src/app.module';
import { join } from 'path';

let expressInstance: any;

async function bootstrap() {
  const app = await NestFactory.create<NestExpressApplication>(AppModule);
  app.enableCors();
  app.useStaticAssets(join(process.cwd(), 'uploads'), { prefix: '/uploads' });
  await app.init();
  return app.getHttpAdapter().getInstance();
}

export default async function handler(req: any, res: any) {
  if (req.url) {
    req.url = req.url.replace(/^\/api\/index(\.ts|\.js)?/, '') || '/';
    if (!req.url.startsWith('/')) {
      req.url = '/' + req.url;
    }
  }

  if (!expressInstance) {
    expressInstance = await bootstrap();
  }
  return expressInstance(req, res);
}
