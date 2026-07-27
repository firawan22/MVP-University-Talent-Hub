import { NestFactory } from '@nestjs/core';
import { NestExpressApplication } from '@nestjs/platform-express';
import { AppModule } from '../src/app.module';
import { join } from 'path';
import serverlessHttp from 'serverless-http';

let cachedHandler: any;

async function bootstrap() {
  const app = await NestFactory.create<NestExpressApplication>(AppModule);
  app.enableCors();
  app.useStaticAssets(join(process.cwd(), 'uploads'), { prefix: '/uploads' });
  await app.init();

  const expressApp = app.getHttpAdapter().getInstance();
  return serverlessHttp(expressApp);
}

export default async function handler(req: any, res: any) {
  // Normalize req.url for Vercel serverless rewrites
  if (req.url) {
    req.url = req.url.replace(/^\/api\/index(\.ts|\.js)?/, '') || '/';
    if (!req.url.startsWith('/')) {
      req.url = '/' + req.url;
    }
  }

  if (!cachedHandler) {
    cachedHandler = await bootstrap();
  }
  return cachedHandler(req, res);
}
