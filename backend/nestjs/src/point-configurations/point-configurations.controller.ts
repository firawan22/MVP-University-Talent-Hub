import { Controller, Get, Put, Body, Param, UseGuards } from '@nestjs/common';
import { PointConfigurationsService } from './point-configurations.service';
import { AuthGuard } from '../auth/auth.guard';
import { RolesGuard } from '../auth/roles.guard';
import { Roles } from '../auth/roles.decorator';

@Controller('point-configurations')
@UseGuards(AuthGuard, RolesGuard)
@Roles('admin')
export class PointConfigurationsController {
  constructor(private readonly svc: PointConfigurationsService) {}

  @Get()
  getAll() {
    return this.svc.findAll();
  }

  @Put(':id')
  update(@Param('id') id: string, @Body() body: { points: number }) {
    return this.svc.update(Number(id), body.points);
  }
}
