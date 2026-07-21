"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.PointConfigurationsModule = void 0;
const common_1 = require("@nestjs/common");
const typeorm_1 = require("@nestjs/typeorm");
const point_configurations_controller_1 = require("./point-configurations.controller");
const point_configurations_service_1 = require("./point-configurations.service");
const point_configuration_entity_1 = require("../entities/point-configuration.entity");
let PointConfigurationsModule = class PointConfigurationsModule {
};
exports.PointConfigurationsModule = PointConfigurationsModule;
exports.PointConfigurationsModule = PointConfigurationsModule = __decorate([
    (0, common_1.Module)({
        imports: [typeorm_1.TypeOrmModule.forFeature([point_configuration_entity_1.PointConfigurationEntity])],
        controllers: [point_configurations_controller_1.PointConfigurationsController],
        providers: [point_configurations_service_1.PointConfigurationsService],
        exports: [point_configurations_service_1.PointConfigurationsService],
    })
], PointConfigurationsModule);
//# sourceMappingURL=point-configurations.module.js.map