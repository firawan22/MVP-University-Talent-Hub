import { Body, Controller, Delete, Get, Param, Post, Put, Query, UseGuards } from '@nestjs/common';
import { StudentsService } from './students.service';
import { AuthGuard } from '../auth/auth.guard';
import { RolesGuard } from '../auth/roles.guard';
import { Roles } from '../auth/roles.decorator';

@Controller('students')
@UseGuards(AuthGuard, RolesGuard)
@Roles('admin')
export class StudentsController {
  constructor(private readonly svc: StudentsService) {}

  @Get()
  getAll() {
    return this.svc.findAll();
  }

  @Get('search')
  search(@Query('q') q: string) {
    return this.svc.search(q || '');
  }

  @Get(':id')
  getOne(@Param('id') id: string) {
    return this.svc.findOne(Number(id));
  }

  @Post()
  create(@Body() body: any) {
    return this.svc.create(body);
  }

  @Put(':id')
  update(@Param('id') id: string, @Body() body: any) {
    return this.svc.update(Number(id), body);
  }

  @Delete(':id')
  remove(@Param('id') id: string) {
    return this.svc.remove(Number(id));
  }
}
