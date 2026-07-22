
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


```
AlaaSystemsHub
├─ .dockerignore
├─ .editorconfig
├─ app
│  ├─ Exceptions
│  │  └─ Handler.php
│  ├─ Helpers
│  │  ├─ IconHelper.php
│  │  └─ MediaHelper.php
│  ├─ Http
│  │  ├─ Controllers
│  │  │  ├─ Api
│  │  │  │  ├─ Admin
│  │  │  │  │  ├─ AnalyticsController.php
│  │  │  │  │  ├─ ArticleController.php
│  │  │  │  │  ├─ CertificateController.php
│  │  │  │  │  ├─ CouponController.php
│  │  │  │  │  ├─ CustomerController.php
│  │  │  │  │  ├─ DashboardController.php
│  │  │  │  │  ├─ EducationController.php
│  │  │  │  │  ├─ ExperienceController.php
│  │  │  │  │  ├─ GameController.php
│  │  │  │  │  ├─ InvoiceController.php
│  │  │  │  │  ├─ MediaController.php
│  │  │  │  │  ├─ MessageController.php
│  │  │  │  │  ├─ NotificationController.php
│  │  │  │  │  ├─ OrderController.php
│  │  │  │  │  ├─ PaymentController.php
│  │  │  │  │  ├─ ProductCategoryController.php
│  │  │  │  │  ├─ ProductController.php
│  │  │  │  │  ├─ ProfileController.php
│  │  │  │  │  ├─ ProjectController.php
│  │  │  │  │  ├─ RoleController.php
│  │  │  │  │  ├─ SettingController.php
│  │  │  │  │  ├─ SkillController.php
│  │  │  │  │  ├─ TestimonialController.php
│  │  │  │  │  └─ UserController.php
│  │  │  │  ├─ Auth
│  │  │  │  │  ├─ AuthController.php
│  │  │  │  │  └─ PasswordResetController.php
│  │  │  │  ├─ Customer
│  │  │  │  │  ├─ CartController.php
│  │  │  │  │  ├─ OrderController.php
│  │  │  │  │  └─ PaymentController.php
│  │  │  │  ├─ Guest
│  │  │  │  │  ├─ ArticleController.php
│  │  │  │  │  ├─ CertificateController.php
│  │  │  │  │  ├─ ContactController.php
│  │  │  │  │  ├─ EducationController.php
│  │  │  │  │  ├─ ExperienceController.php
│  │  │  │  │  ├─ GameController.php
│  │  │  │  │  ├─ ProfileController.php
│  │  │  │  │  ├─ ProjectController.php
│  │  │  │  │  ├─ SkillController.php
│  │  │  │  │  ├─ StoreController.php
│  │  │  │  │  └─ TestimonialController.php
│  │  │  │  ├─ HealthCheckController.php
│  │  │  │  └─ Public
│  │  │  │     └─ ArticleController.php
│  │  │  └─ Controller.php
│  │  ├─ Kernel.php
│  │  ├─ Middleware
│  │  │  ├─ AdminOnly.php
│  │  │  ├─ CheckPermission.php
│  │  │  └─ TrackVisitor.php
│  │  ├─ Requests
│  │  │  └─ Auth
│  │  │     ├─ ForgotPasswordRequest.php
│  │  │     ├─ LoginRequest.php
│  │  │     ├─ RegisterRequest.php
│  │  │     ├─ ResetPasswordRequest.php
│  │  │     └─ VerifyOtpRequest.php
│  │  └─ Resources
│  │     ├─ ArcadeGameResource.php
│  │     ├─ ArticleResource.php
│  │     ├─ CartResource.php
│  │     ├─ CertificateResource.php
│  │     ├─ CouponResource.php
│  │     ├─ EducationResource.php
│  │     ├─ ExperienceResource.php
│  │     ├─ GameScoreResource.php
│  │     ├─ InvoiceResource.php
│  │     ├─ OrderResource.php
│  │     ├─ PaymentResource.php
│  │     ├─ ProductCategoryResource.php
│  │     ├─ ProductResource.php
│  │     ├─ ProfileResource.php
│  │     ├─ ProjectCollection.php
│  │     ├─ ProjectResource.php
│  │     ├─ SkillResource.php
│  │     ├─ TestimonialResource.php
│  │     └─ UserResource.php
│  ├─ Mail
│  │  ├─ ContactMessageNotification.php
│  │  └─ OtpMail.php
│  ├─ Models
│  │  ├─ ActivityLog.php
│  │  ├─ AnalyticsEvent.php
│  │  ├─ ArcadeGame.php
│  │  ├─ Article.php
│  │  ├─ Cart.php
│  │  ├─ Certificate.php
│  │  ├─ ContactMessage.php
│  │  ├─ Coupon.php
│  │  ├─ CustomerOffer.php
│  │  ├─ Education.php
│  │  ├─ Experience.php
│  │  ├─ GameScore.php
│  │  ├─ Invoice.php
│  │  ├─ LoginAttempt.php
│  │  ├─ Media.php
│  │  ├─ Notification.php
│  │  ├─ Order.php
│  │  ├─ PasswordResetToken.php
│  │  ├─ Payment.php
│  │  ├─ PaymentMethod.php
│  │  ├─ PersonalProfile.php
│  │  ├─ Product.php
│  │  ├─ ProductCategory.php
│  │  ├─ Project.php
│  │  ├─ Role.php
│  │  ├─ Setting.php
│  │  ├─ Shipment.php
│  │  ├─ ShippingAddress.php
│  │  ├─ ShippingMethod.php
│  │  ├─ Skill.php
│  │  ├─ Testimonial.php
│  │  └─ User.php
│  ├─ Providers
│  │  └─ AppServiceProvider.php
│  ├─ Services
│  │  ├─ CartService.php
│  │  ├─ OrderService.php
│  │  └─ PaymentService.php
│  └─ Traits
│     └─ ApiResponse.php
├─ artisan
├─ bootstrap
│  ├─ app.php
│  ├─ cache
│  │  ├─ config.php
│  │  ├─ pac19A6.tmp
│  │  ├─ packages.php
│  │  └─ services.php
│  └─ providers.php
├─ composer.json
├─ composer.lock
├─ config
│  ├─ cors.php
│  ├─ database.php
│  ├─ mail.php
│  └─ sanctum.php
├─ config.backup
│  ├─ app.php
│  ├─ auth.php
│  ├─ cache.php
│  ├─ cors.php
│  ├─ database.php
│  ├─ filesystems.php
│  ├─ logging.php
│  ├─ mail.php
│  ├─ queue.php
│  ├─ sanctum.php
│  ├─ services.php
│  └─ session.php
├─ database
│  ├─ database.sqlite
│  ├─ factories
│  │  └─ UserFactory.php
│  ├─ migrations
│  │  ├─ 2026_07_14_190709_create_roles_table.php
│  │  ├─ 2026_07_14_190821_create_users_table.php
│  │  ├─ 2026_07_14_190936_create_personal_profiles_table.php
│  │  ├─ 2026_07_14_191014_create_skills_table.php
│  │  ├─ 2026_07_14_191141_create_experiences_table.php
│  │  ├─ 2026_07_14_191222_create_educations_table.php
│  │  ├─ 2026_07_14_191317_create_certificates_table.php
│  │  ├─ 2026_07_14_191359_create_projects_table.php
│  │  ├─ 2026_07_14_191502_create_testimonials_table.php
│  │  ├─ 2026_07_14_191539_create_product_categories_table.php
│  │  ├─ 2026_07_14_191618_create_products_table.php
│  │  ├─ 2026_07_14_191702_create_coupons_table.php
│  │  ├─ 2026_07_14_191736_create_carts_table.php
│  │  ├─ 2026_07_14_191812_create_shipping_addresses_table.php
│  │  ├─ 2026_07_14_195124_create_shipping_methods_table.php
│  │  ├─ 2026_07_14_195156_create_orders_table.php
│  │  ├─ 2026_07_14_195241_create_payments_table.php
│  │  ├─ 2026_07_14_195403_create_payment_methods_table.php
│  │  ├─ 2026_07_14_195747_create_invoices_table.php
│  │  ├─ 2026_07_14_195834_create_shipments_table.php
│  │  ├─ 2026_07_14_200019_create_customer_offers_table.php
│  │  ├─ 2026_07_14_200357_create_articles_table.php
│  │  ├─ 2026_07_14_201202_create_media_table.php
│  │  ├─ 2026_07_14_201344_create_arcade_games_table.php
│  │  ├─ 2026_07_14_201508_create_game_scores_table.php
│  │  ├─ 2026_07_14_201557_create_contact_messages_table.php
│  │  ├─ 2026_07_14_201907_create_notifications_table.php
│  │  ├─ 2026_07_14_202028_create_settings_table.php
│  │  ├─ 2026_07_14_202239_create_activity_logs_table.php
│  │  ├─ 2026_07_14_202309_create_analytics_events_table.php
│  │  ├─ 2026_07_15_054426_create_sessions_table.php
│  │  ├─ 2026_07_15_054509_create_jobs_table.php
│  │  ├─ 2026_07_15_054538_create_cache_table.php
│  │  ├─ 2026_07_15_091733_create_personal_access_tokens_table.php
│  │  ├─ 2026_07_18_072756_create_password_reset_tokens_table.php
│  │  ├─ 2026_07_18_072903_create_login_attempts_table.php
│  │  └─ 2026_07_22_082133_create_personal_access_tokens_table.php
│  └─ seeders
│     ├─ AdminUserSeeder.php
│     ├─ DatabaseSeeder.php
│     ├─ PersonalProfileSeeder.php
│     └─ RoleSeeder.php
├─ docker
│  ├─ default.conf
│  ├─ nginx.conf
│  ├─ startup.sh
│  └─ supervisord.conf
├─ Dockerfile
├─ package-lock.json
├─ package.json
├─ phpunit.xml
├─ public
│  ├─ .htaccess
│  ├─ favicon.ico
│  ├─ index.php
│  └─ robots.txt
├─ README.md
├─ render.yaml
├─ resources
│  ├─ css
│  │  └─ app.css
│  ├─ js
│  │  ├─ app.js
│  │  └─ bootstrap.js
│  └─ views
│     ├─ emails
│     │  ├─ contact-notification.blade.php
│     │  └─ otp-notification.blade.php
│     └─ welcome.blade.php
├─ routes
│  ├─ api.php
│  ├─ console.php
│  └─ web.php
├─ ssl
│  └─ aiven-ca.pem
├─ storage
│  ├─ app
│  │  ├─ private
│  │  └─ public
│  │     └─ media
│  │        ├─ articles
│  │        │  ├─ 0yQAPhR52o72FFHdLn4sJhhSGHwmIarypj5a0EA9.jpg
│  │        │  ├─ 62eJw2EFslvXq2KDUxp25XCtU3wcZXAXnINYPuXl.jpg
│  │        │  ├─ 72I1evt7hmFNMM5GhzOZBPkPgBBjT679WnVaBmDO.jpg
│  │        │  ├─ EGQV2dMPeGAyu24dMRlVWZyyLfsbIvxFLiof04Wu.jpg
│  │        │  ├─ HkofHQbmPmnrBTmHUVJ8C5vN33L8MZvQZWfdWKVR.png
│  │        │  ├─ Mn8p5u06zGamxsWBPjzOu6O0XF0kIEUrmVf6M9W4.png
│  │        │  ├─ TJQchlUgzqMpGLwtjQwntmDe8VA1DwPUd2i3ZWqw.png
│  │        │  ├─ tLkFOOxWaoF5yg5t0dNuLJQxz1fCL0iigXjo6bgS.png
│  │        │  ├─ TX9WJTM7a9QFJEofxjyFhyQmOenHf5JPOCAO4BBb.jpg
│  │        │  ├─ xgddGLezZC5sFpr8q0iGhRH1TDYPSOoOivoeTAyw.png
│  │        │  ├─ xvHWcw03JZxJmNG0tdivpdEt04bmQNgJgUaXxfgA.png
│  │        │  ├─ YNXHxw6fli7QrOT8OV8TCWUSnVWIx8Ttihlrsm1m.jpg
│  │        │  └─ yoa6okWSZ3gTuXgcBkf92HcO29rr0GCKVgE0A62N.png
│  │        ├─ certificates
│  │        │  ├─ 1M5ZcM17ynI2mUh0y2gsQxi8pRaOiSW2eJSjDnkk.png
│  │        │  ├─ 36FY575xBEvmXMPFcXKTlzC5mbXcaq5dbTUJVIHk.png
│  │        │  ├─ 3LQAGSzowS4yY5lAROvKn7Q8LV8wbhZ5GcBJ6RVL.jpg
│  │        │  ├─ 3xIb4NxCneFnO4PTXEOZJb9WGkrCITn4n2mIAVFh.png
│  │        │  ├─ 5lAWb9cHtZchfaI7rBJDgguqVApmnqec43Fkn1Gu.jpg
│  │        │  ├─ 6HGhwIQbcF5R27vj81e00Q7rQJX9o13HZgV58cFd.png
│  │        │  ├─ 8JcS3gPBPp4aLN62zgBqshDZ8uMrojp0anGk3DMl.png
│  │        │  ├─ cRnPtHLKSlojmhgg1GadBAXFsTqRj0RTtVwpG7fo.png
│  │        │  ├─ dAKUt7ZFK3KLTtMgU3AVrZNMMDAVS2gtKEzhYJyT.png
│  │        │  ├─ DbuPzXo7civSojfORqYkW6MkIARTPX0WFEVB97b0.png
│  │        │  ├─ i70PdCbxoSjE1v8v3ZtovQC2NGhVMxqZfryOfw0n.png
│  │        │  ├─ iI2mxZlur8WP0F6I0pm8JlH1JuMiZItd3eMimCjZ.jpg
│  │        │  ├─ JBEe7EZ4oYkEj0nBZUhf4PDhs1bHE6whEVJibkDb.jpg
│  │        │  ├─ jCs1vZyGToYZKkBR2JUpoMpGHiuuLxCok5oF1yYu.png
│  │        │  ├─ ka6hT9fZVOwqEts70s6UbOjhCrzDOgIYYRy18BP6.png
│  │        │  ├─ M8sAFeAtfSYF8R0uy0Z1zct3sRPNhpUo1Ra97QLR.png
│  │        │  ├─ Mn8tDuQ2Gt7KREMh9Ae5cac2rqBXRl7WrO0RryBF.png
│  │        │  ├─ P4Qppb9RQroshYW5RNXYH9StnPbzeUY6RNHa991a.png
│  │        │  ├─ tYlqO2ov4RhHt1CLJCCU31DGYwf2R6wdw1HO9KxL.png
│  │        │  ├─ U7pkykPNQyHGObTtHUqRvoiUfWcjlxDAAcZ5zLI6.png
│  │        │  └─ wOt55j2WkWfUtIpbUiz3TRnQXBhR68peY2TOVR49.jpg
│  │        ├─ cv
│  │        │  ├─ 0lsEDVlBRSenEoCFMdMM6n4pYTblUm2Pf9kl7Rfa.pdf
│  │        │  ├─ CKqewYZHvOKb8XwcVtgkVIbJhdqMPNrQg9WHVLVg.pdf
│  │        │  ├─ ea1kYrq0tq5JZzyIWseCpRYvu3hG03Hb2u6Of9dD.pdf
│  │        │  ├─ gSZEdKqAAkBppawPBCI5R6R6xARrrKgLvVP47SkB.pdf
│  │        │  ├─ hDwdWVzz9J1Qpu1NWlO6MrE4xjQ6zFucxwil13YP.pdf
│  │        │  ├─ NNEuhrOzCYALKzN0aYUPrxt5T5oLeAJ6V1bW7ayX.pdf
│  │        │  └─ S4uTy5AsTMEFbV6Jqs9EhTiJb75ANhherQH95SZI.pdf
│  │        ├─ education
│  │        │  ├─ DipmA1sGUu1dTZ5RUTifFzrHFOmn736f7Vb8gdbQ.png
│  │        │  ├─ DOd5FiQwfafA8WlBQkqdATGUf8yZQdaPHLzWzVco.png
│  │        │  ├─ hQ95bf51z3M9CAmnzLKnTpBKsxgd61V0SooBkJx8.png
│  │        │  ├─ kFkQFSC8mxkCOnrjbHwnObPrqnnZrqTg2ESE0lPI.jpg
│  │        │  ├─ lbgvppk6rRoneaI3VaGLCEvwqj79iw21qN8ScvP2.png
│  │        │  ├─ LeWAo8f1pjMqdmuXIOfRdlmAcmYr8HVSdiWxoAAT.jpg
│  │        │  ├─ nwUhmOmP2ihRYOr1L9YrznoUcI5muxJuHXlDstlP.png
│  │        │  ├─ p7RW4Z4880QbuWcKR4nvrBWBZTk7BJg1CgKZDmOY.jpg
│  │        │  ├─ rdjsJSV8M6CjUSXbjaSzxuVYqIdPJMVbajHUGmkc.png
│  │        │  ├─ SwCfi7uJ8QQzdsUNz0gWQrwbFvWVCQUSCFePEgUO.png
│  │        │  ├─ WPSmx4SOeJqIJCovkPkUjU8FYwFQuXynDG534bIV.jpg
│  │        │  ├─ x7HxVmlM3MsAmYb7In2BF3QAuODDShc3zjQODijG.jpg
│  │        │  └─ ZfNYxOETKyJSDkF2vxWnSnq1pAjIN1WhBe9u1T1w.jpg
│  │        ├─ experiences
│  │        │  ├─ 81K8MXmRS4MvJrm0OdgRsLoDfnr0L90jJ9jA92W9.png
│  │        │  ├─ cIidM5InHBT0ElDnW7VMBBl8AqTm1wN3hiw5Uubx.png
│  │        │  ├─ jc4WOuKqK6XHBmf5amB1Z4xMMGnURzoLNqkTE7Id.png
│  │        │  ├─ JiXHI0UG5TickTWLvEOYgmbAlYITX0nKtkXnltEW.png
│  │        │  ├─ PaDD4q4GmzABUpbm3kQEKyNuekPbnYF2gpZMLBo3.png
│  │        │  └─ T1dokou1v4pkqQUYD1Hsx9Q6kUKsJs4tlnoxiqZm.jpg
│  │        ├─ icons
│  │        │  ├─ 0ASTyFQWaknPIpfnCTx3MzZj9whW8Z0pGd79ZYiT.png
│  │        │  ├─ 2J8byiTMPWyZGDfFIl5qcWskvRgPlOktuhcPPjDD.png
│  │        │  ├─ 69I7JHPNPYzL24JBVnkMynmHwFihS0eMzF3zWS01.png
│  │        │  ├─ 939F52nBImTJHiPjGE7gZbk9CqC5jOjhgGQcTf5h.png
│  │        │  ├─ 9S9f7F2MKbcolLlMpasMpzr1FXKkw7EbEHBA1ZKK.png
│  │        │  ├─ L3ftn6as76qRL8NhE2KNmWne8COIXlrCQfz4AbsC.jpg
│  │        │  └─ l3GD7tXAx8Dq4spTlty8rHR13OCMCCSbA1R3QMYL.png
│  │        ├─ products
│  │        │  ├─ 2rtfc5C8A6xhEuUMH0YhWHqFrGAQd5D8qU3X5Tdj.png
│  │        │  ├─ FSiMZo0iHl9iMsmIOlL5jgLmbDkUNldZI8PsfZep.png
│  │        │  └─ hK6jg2tkINonPB1z5LmD1PhV3UsKcMoSLOQqm65V.png
│  │        ├─ profile
│  │        │  ├─ 1L9RPh9i0gIuBNcaI7OOoHNfF1XUcN3ww4hb3mcP.jpg
│  │        │  ├─ 1QWq5uuHuuiuNl2AAU1w8S3GjbzSPqudULmD0MEw.jpg
│  │        │  ├─ 48Dw0NWAFMRR54wNKxsxPcZsUqrHWbzSm4pLkspv.jpeg
│  │        │  ├─ 6UQI56JHrTsefrL8qY5BDkbiFpoBDuCMNF601c2K.jpg
│  │        │  ├─ aZro9ojbik4eFIZI9qSGfNkdU4zHSZuG5N1yx8oh.png
│  │        │  ├─ Ef11PY76eSkX2TeWGsnzkl6rFtOhT2bsqonCei6I.png
│  │        │  ├─ eUG00JD6vXzyWGwDwOtVMqVwnBnzi8qzTfS0508V.jpg
│  │        │  ├─ GdL327gvUvphjii5d2WPy1vpwEFIpBp8HtqMFmVK.jpg
│  │        │  ├─ InyTO5WL9SkVu4Mu0sNemBuAOR4uNy75AoUoaUTZ.jpg
│  │        │  ├─ jlx99aMmQ8rEJUKjUK9cuJBIgEpc17o8Zht4myw6.png
│  │        │  ├─ jr0GtpHUPggjUuPXNZjoe65EerxlZp8K76uDPB6C.png
│  │        │  ├─ KGD9o1v0XC0a8ULPl0exDfbxxKFm5nQ8flFUuZ6Q.png
│  │        │  ├─ R91RiKmuPYZb3KORSx6BzSJnAD3fUWQlIEu2YS5c.jpg
│  │        │  ├─ rT8cZJeeEiguLxAQtClcT5rkqabGIoId78ST8ykW.jpg
│  │        │  ├─ rWyvljQYksaKSIMp7JKKJpe4oZ8cPcpI4YEHbmw0.jpg
│  │        │  ├─ SY4FULT6JXmvodI58pwIVz7yj1nrtkguYLPkhzA2.jpg
│  │        │  ├─ TVZrZDYjPubd28Ertpwo9f0C4uttjnjhWTJ2e2pO.png
│  │        │  ├─ UMr2eMBnT4GMTGyGQuGZ5XFkiNQHCjQSEcGoBjh0.jpg
│  │        │  ├─ V0yLaQyJzyIEB93m11KCBbo8lrBtg2gWjIJVCy9y.jpg
│  │        │  └─ ZqFtJNEjO2IREWmScNBZaO24tEFQ3PNZDVxXpHgM.jpg
│  │        ├─ projects
│  │        │  ├─ dBKZe2yBVyueDC0rM5NAzoCHih8TNhHDEax3bjKV.png
│  │        │  ├─ Ppocn5Ek97ANPb8xD83fRC3dhfxbBRaLtBQ876Ev.png
│  │        │  ├─ Te9VJYDbmbzKg109CYTBIuKya0pX4XBG3U3FBGyd.png
│  │        │  ├─ Tx3HJufQqZfVqQ1IKyJTO4PHCHAB0jS8t6p3ekjJ.jpg
│  │        │  └─ VRr2Sq471CvkN8ji3WjXvaTyHZyIjcAWL9Qy7t5F.png
│  │        ├─ seo
│  │        │  ├─ Auh3RCtzo5jxiJxamtTnrWDXH9VFBe7HEKyToA3T.png
│  │        │  └─ ZSOOZqxnPhwvUwszCbPWGsRXXy1NbFk2yMYKfqsC.jpg
│  │        ├─ social-icons
│  │        │  ├─ 3L7yc9eA8QHQjg6qJ8OaJuBOGU4dj2ZvTQSIH0D5.png
│  │        │  ├─ 4nBIPjQjSNkVz6vOIO2hdlAwgNjoMpQ4uumokWtV.png
│  │        │  ├─ 5kmmBhhNV3hT45IEUW6MMCp19SubuUuZMavsJerB.png
│  │        │  ├─ ihmsbKyzJLWgjWPozFar9yBF7gMoYyh30NEHv5Wx.png
│  │        │  ├─ MzxvS29zY5vH5B9kM6xqNTvSQsTjebmjlrNYNFQW.png
│  │        │  ├─ QT9Ubje6641StcJA2BE2hr4CfsyeiP3aEaFJkdW5.png
│  │        │  └─ xEkb2J49pqHfkbt2zdYAmEzoGqLDej4WinIel82w.png
│  │        └─ tool-icons
│  │           ├─ 3MAvUxlnKvFDCUf3RnOO2ib1iJCd9lnLtCcoPKoS.png
│  │           ├─ 9aSEywsf3rg3FccnzreC1vORUnxRvetoFxyEyq68.png
│  │           ├─ bWwk9QtZQlBATr3nhJZKnnqVPbO5vz4bz7HnOzmO.png
│  │           ├─ CuArbI9b9m61wSh6ED4eLcwZdAs3sv0l9zuZxT0C.png
│  │           ├─ ettFJGym5raKMLXOyf5VAwnaqTtCLrN10ZZL1ut9.png
│  │           ├─ fAlcIbqF9FH596hdPwbznnFrxHZYKl4e6fOumpH9.png
│  │           ├─ JcbEcf95F3raLyxJ1iDEgKS3XCrVntwK4eNcSlAI.png
│  │           ├─ kEGmAvHxQ2tDIGYjH9PS6WWkXPVxrYLX6qrhbYsW.png
│  │           ├─ LWtttsouLmKkn8sC4laiGptRWI6kHF0SSV2IZhZC.png
│  │           ├─ oZdOnNWB8exue0sEc04DvSx3fSK1nM2deHC5E0mx.png
│  │           └─ QSZArrMJyXChe3qgKgDbkwHpQGy9g0f8qhUWrl0A.png
│  ├─ framework
│  │  ├─ cache
│  │  │  └─ data
│  │  ├─ sessions
│  │  │  ├─ 0IqP65ZIOn9tHaVt9wCudS48fE1KSgIOKVLbEiTw
│  │  │  ├─ 13vN8Hrof3Pj6VHRrldw5JuhPM6djHOlWj1xGQzN
│  │  │  ├─ 197GBi1KJaxtmFmLofURBRZvZ0egkebkvldo4Hw8
│  │  │  ├─ 1H0lmvV6yZderIW2fZ67T0NDs1szjuFfUiwmiNeW
│  │  │  ├─ 59HKaR1umM8kPhBvwHGwgpVMjcdKpROQ8CFOHxAQ
│  │  │  ├─ 5eYGwvgi2fcYS02Bmuljd5bbkAahxR87bAhdi4mA
│  │  │  ├─ 68cP4OM74lHN4NjDOaDG2ZRdzZS6GT3w0BWrZ5Av
│  │  │  ├─ 7sqxUW8dfpWvKnOlDOBJqssIFMnXpunxpjVP14ae
│  │  │  ├─ 7V6B150TTHVWof7aLp0aiVHkt9ic9SIcWdy5A639
│  │  │  ├─ 9UEZACjzgfV2QNZx5Q3milaSqA77E4SLOVfOfeOv
│  │  │  ├─ 9vFC2xeHzk0L3OAcWjev9BQqFSWU5nm8uOBkjsZE
│  │  │  ├─ 9ZcXMVoN9hkq1WJx50HedMHYiEMzSitzO6aoeFqN
│  │  │  ├─ AEm6pEpjTPozagImhvTlgNyQRGvQjjOpyCmx4eMD
│  │  │  ├─ axg6nQqG3clqr3vVSC9gcvOnfxkgGd1c48urV3a7
│  │  │  ├─ BFIXncDYCzPqFauihlCrpbMJcJSAdIwoLrKhAlVC
│  │  │  ├─ cMEia7EQ0hdIyvnI3qMNCPp0dOljisK7NVAZELGI
│  │  │  ├─ dNfXUM7VrUEsIbPmtSVKEtrra1795Vkde3DCS5ui
│  │  │  ├─ DNW4SU6mzmGuYg51LtshempoHrJErqkrG56tSdUr
│  │  │  ├─ DvkGqR9Q0bH91VRFjNjxXJL0QA95Y7ssTBulP4d9
│  │  │  ├─ dZ2sTm001NxjnV5E39zvBsK3coNDCIVaf91Egyqq
│  │  │  ├─ Ejqr5ZnQHrDb0b0VeAneQQVFKeQesfqX997EEmnO
│  │  │  ├─ EPHDRyimKP6l0sLcpFZnG7jhnM2ZhJyZDMUvqeU3
│  │  │  ├─ ErjjyME8WGkKmwFpE3EI6e3iDjpFdRLyV5Qy7JPf
│  │  │  ├─ GQBLQ2qqypoyjeBEZ5SJk7B29z68U5oX2acQdeEp
│  │  │  ├─ gX3zBLwTETAnWMqFxag9GriSRgNfAsLzAn2W9qY2
│  │  │  ├─ hsv39DjBy6u7UjWEOM2jwjUDGscFr5EJukAZbMGb
│  │  │  ├─ j4Al8vh1pt5RAkpldnkPgL7Xa8OhqQlUg3GkZ77F
│  │  │  ├─ JaK0VLB354u41NlUkyfvWueirnCLBzE9t2ndiPSW
│  │  │  ├─ JF3LYXM0Cim1JLIsNQtYQK0baLqN9QizbLGNQV51
│  │  │  ├─ JFHsqLKtaNS7prvcMA7R6bn9NDbGgrRmmkRmlMXb
│  │  │  ├─ jimGqOoO5zqJt5xCOJccNNNIOXrVexBevucp7WVK
│  │  │  ├─ JLrYel7qzZ6K4j7XDBhCh6EXT3ZC8lhS0l2YnTHp
│  │  │  ├─ jPuCgrHPjkoRwbA8WSEgy354BFq6bR1MWiRmJY6C
│  │  │  ├─ jYa7PLd30TmvmvH6aaVCgpATAaSCsVW8RPN7zdje
│  │  │  ├─ keBzs0xjCE9H6N2mbJRZ18rxOetdPcOJIkDkye1C
│  │  │  ├─ KF8WhRmhPGghxRFXAru3dn9ARb9GDmontSNzs72n
│  │  │  ├─ KKe1hJXzGjo7TvBQlK9qmVD2RWzcwuZTUp1HmjXp
│  │  │  ├─ kSp15K8zJUZHeY6ORVAq6DcU8KIRLCJ6i2J3M0nN
│  │  │  ├─ KTN9IRes4bEeZYAvaX2v3ucb6kS8eUgiOeGc0Tbw
│  │  │  ├─ L39uNtFi9XTUp7aAP4KltGfOqK0RIEq8me1q7VBb
│  │  │  ├─ lis7IwFYkWJX1u852EpYiVSyekfsl2RQPZeF0Vh8
│  │  │  ├─ lnfJsueBu08mdO8i8tCvBS9F0N44DdMKijRoVGOf
│  │  │  ├─ LRsxEFZkKCin5FUrXSPhc7JkRHMRuZtslpP2xYt1
│  │  │  ├─ lS55ae8WpE8VCyuQQUhGI46xTQ1biSkICsBB8yHS
│  │  │  ├─ mC7fOFPdDlKWyNqMuXa8C0UYVioNjBC2KqkCxX4s
│  │  │  ├─ mSrjyW9itsXVhLH3iq0fu7LIiqhV9HF9UJp8nPPn
│  │  │  ├─ MYGHJ7n5V4W0ySZrQnfXbxsH3OODj2EGbVecH8Q2
│  │  │  ├─ MyYduVioJwlCEpFHeHqUkxEPZVPH5TGVvvrVQjtE
│  │  │  ├─ N6jv1fWq2o800IN5ILKPW5I2NWhTkf0sECjatCaT
│  │  │  ├─ NdYyKUBHlj02mdXLcb1P6ASOR8nfqiGuR3D0yTT3
│  │  │  ├─ NzYlqVpnDeB5BFmvisaqa0AZWfwN07ieDmOHpDcp
│  │  │  ├─ OFfw8od6oc72lodI42LA9wB7xhw8aze47cwGgFHU
│  │  │  ├─ okCzN3G0EkdjHwi5qDfPXI7gTNVdAgb8SqMeGsYL
│  │  │  ├─ oMHwo4hhswj1S1xQOn14klIzdeM0PC3NAQeHLS9s
│  │  │  ├─ oWXNZyuHwCJPSsWxvwav1mwbwAEHrIyuDxaJMRf6
│  │  │  ├─ oxxFPKPHYxAWn3T1sZQtKLqREizaHVsGDeXxCzAs
│  │  │  ├─ ozzX0eP0VbOVyVf9ygW3FPZeQ35ql59vli1mkl3P
│  │  │  ├─ P5N6zuPARrzQnAxpCC88oA4LFDN1ALJQ5gpiVINN
│  │  │  ├─ PO8R5XWMwbQaJu1j2i3XHX2k8Gku4uF7URKBypnk
│  │  │  ├─ r3MP2Oo3xcQfyqD7zQzIjdwMyGLGJJhWSyDZ0MWf
│  │  │  ├─ r7PvmQmZfOtduGdYFYqI1mfwpfWUPQvHbPiP2IFc
│  │  │  ├─ RE6Dg8cmpDhnNwzCNQKbt2mIseAJMnkoLNMGkSzB
│  │  │  ├─ SS1n8rL6IQtAooS7e7l1pcM2jHQh1M70YOKi9SIM
│  │  │  ├─ sZAihqUmgwvjGjt7Kg69vxW4wbGmr7GE04AHiBMQ
│  │  │  ├─ t2XuXoIqJL4g1FCWzi1Amaio9JgneOty3XKcS7Yl
│  │  │  ├─ T8JYodA5O5jQJs4iqMNKxA38zRtIGh2bYaivwlKn
│  │  │  ├─ TkVJJZVuNXJWikNAlNkYqtcbkrLprICqDwYPKO60
│  │  │  ├─ TSM1SiQgP88XAzJBebQx1y9wHbRmQ9PnBdUbi8Ld
│  │  │  ├─ TtvGZJjXpdzarGSnwF2mrJxVFrLW6z0bnqBRRE9h
│  │  │  ├─ UbuIX0EbmQdXMBQd1zUQt2Vdv2dgJeJgXhhvu68m
│  │  │  ├─ uCILvCG1m9W5W6oaaRjjvI4fjBOnrLqfgHFhYx9T
│  │  │  ├─ umXAIOieX4W8KTFZEvDejshAfftsVofFJ2Q65BUS
│  │  │  ├─ vbNdre52X1rkcUztpIr77R96ub7iOTUJbE4w4ZXU
│  │  │  ├─ vm5Rrd1YUqZnffMFJbzY57G0dnoZbk7xfZb73eF1
│  │  │  ├─ VnK9cojaJObrzjE0NQLTlBGgGTS9GKdn23Cdg9hp
│  │  │  ├─ W93q9yBRyt1YazeWNCcoASnHmRq9S8uJkOr2LAvN
│  │  │  ├─ wcG58eJLCRS0LUWfcdP0sikLNAt6wLCfTJzvrqRr
│  │  │  ├─ WEqlE3ABpayxUcwksGfWqSo8dEB39ZATeWA2SODU
│  │  │  ├─ WkeEYHb2pKTN9fOlw0NN05QCJJvULgmJ4j4qaACS
│  │  │  ├─ wTODxojHpA199p3LRxIFb1sSaQmClw3KClWaO26N
│  │  │  ├─ WyLYo6rp2O6Hv2ZGOBaC0HQ2EFzr8kfjRYTOToHQ
│  │  │  ├─ wZPe9I47PBYfaIADtvyuf83p7a5WzY0hudA6nQyM
│  │  │  ├─ X6d9T2uISJCVVJowMsx3IhfcruAmsh3Q2vsLh8Ni
│  │  │  ├─ xaIOUrxoJL9pONb2rZnJKd1RtrLDD5dfCLopWCGG
│  │  │  ├─ XeNOGSP1CWNUREYKZKUBzClBFHXozToPrWibbnPC
│  │  │  ├─ xGBltBljkGMfPKRrxyOccAxfmvACrJDVkv60e5Vl
│  │  │  ├─ xSeHvHPkbGPwEZkH5vKBm3KttpYn1vQ7f2Mqa7ap
│  │  │  ├─ y8KOJJhxmUJXofIyPkFt6CmTTcQU7txM82n5GsCk
│  │  │  ├─ yCS1k05nF2Qw4SgXGpzm6MyMiVMWaRhIcVssEsVl
│  │  │  ├─ yD5FVVJsTHT22kEAQcXaSLUfniYgTB3AwE7K1hoH
│  │  │  ├─ yj5gZ7WTJLftrqIoLq6ZugtxfUQedWC0FcsM0wv4
│  │  │  ├─ YnQCdtl6yBRS8X5qobTSGzRVCMp14DSCh1Z4CNCk
│  │  │  ├─ ySMeiuWEoeoEf4TKt60b4BWqdanweQAMs5ClXvcZ
│  │  │  ├─ yXE5RCDQjlXcqacKAqsNKngfWMvb3x3otEfZRK5D
│  │  │  ├─ zb14RRpFR3s7CF95cJuKLpyTya4QjcFdatmfVct7
│  │  │  ├─ ZDHGzXTIGvnjfG8GMfQRCGXB6mwTnYpwO1mkYIHc
│  │  │  ├─ ZlPOzH7gzEEmp1gfZcd2fTK7xam14I1IjI9NICVs
│  │  │  ├─ zMoYmDkpKtpnP7f17Fi0oo4DRv158cKveB3337zs
│  │  │  └─ ZTbI3j8HaLh4wyIYxI2San9ghlpA0WO4PV0QNR9z
│  │  ├─ testing
│  │  └─ views
│  │     ├─ 3ed9da2b036fc3998bab6e4609d8e41e.php
│  │     └─ b159ae556812ab908fe7ad0f64206ca6.php
│  └─ logs
├─ test-db-connection.php
├─ tests
│  ├─ Feature
│  │  └─ ExampleTest.php
│  ├─ TestCase.php
│  └─ Unit
│     └─ ExampleTest.php
└─ vite.config.js

```
