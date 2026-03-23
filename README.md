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
├─ .editorconfig
├─ app
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
│  │  │  │  │  └─ AuthController.php
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
│  │  │  │  └─ HealthCheckController.php
│  │  │  └─ Controller.php
│  │  ├─ Middleware
│  │  │  ├─ AdminOnly.php
│  │  │  ├─ CheckPermission.php
│  │  │  └─ TrackVisitor.php
│  │  ├─ Requests
│  │  │  └─ Auth
│  │  │     ├─ LoginRequest.php
│  │  │     └─ RegisterRequest.php
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
│  │  ├─ Media.php
│  │  ├─ Notification.php
│  │  ├─ Order.php
│  │  ├─ Payment.php
│  │  ├─ PersonalAccessToken.php
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
│  │  ├─ packages.php
│  │  └─ services.php
│  └─ providers.php
├─ composer.json
├─ composer.lock
├─ config
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
│  │  └─ 2026_03_14_231558_create_indexes_for_all_collections.php
│  └─ seeders
│     ├─ AdminUserSeeder.php
│     ├─ DatabaseSeeder.php
│     ├─ PersonalProfileSeeder.php
│     ├─ RoleSeeder.php
│     └─ SettingsSeeder.php
├─ package.json
├─ phpunit.xml
├─ public
│  ├─ .htaccess
│  ├─ favicon.ico
│  ├─ index.php
│  └─ robots.txt
├─ README.md
├─ resources
│  ├─ css
│  │  └─ app.css
│  ├─ js
│  │  ├─ app.js
│  │  └─ bootstrap.js
│  └─ views
│     └─ welcome.blade.php
├─ routes
│  ├─ api.php
│  ├─ console.php
│  └─ web.php
├─ storage
│  ├─ app
│  │  ├─ private
│  │  └─ public
│  │     └─ media
│  │        ├─ articles
│  │        │  ├─ HkofHQbmPmnrBTmHUVJ8C5vN33L8MZvQZWfdWKVR.png
│  │        │  ├─ Mn8p5u06zGamxsWBPjzOu6O0XF0kIEUrmVf6M9W4.png
│  │        │  ├─ TJQchlUgzqMpGLwtjQwntmDe8VA1DwPUd2i3ZWqw.png
│  │        │  ├─ TX9WJTM7a9QFJEofxjyFhyQmOenHf5JPOCAO4BBb.jpg
│  │        │  └─ yoa6okWSZ3gTuXgcBkf92HcO29rr0GCKVgE0A62N.png
│  │        ├─ certificates
│  │        │  ├─ 1M5ZcM17ynI2mUh0y2gsQxi8pRaOiSW2eJSjDnkk.png
│  │        │  ├─ 36FY575xBEvmXMPFcXKTlzC5mbXcaq5dbTUJVIHk.png
│  │        │  ├─ 3xIb4NxCneFnO4PTXEOZJb9WGkrCITn4n2mIAVFh.png
│  │        │  ├─ 5lAWb9cHtZchfaI7rBJDgguqVApmnqec43Fkn1Gu.jpg
│  │        │  ├─ 6HGhwIQbcF5R27vj81e00Q7rQJX9o13HZgV58cFd.png
│  │        │  ├─ cRnPtHLKSlojmhgg1GadBAXFsTqRj0RTtVwpG7fo.png
│  │        │  ├─ dAKUt7ZFK3KLTtMgU3AVrZNMMDAVS2gtKEzhYJyT.png
│  │        │  ├─ P4Qppb9RQroshYW5RNXYH9StnPbzeUY6RNHa991a.png
│  │        │  ├─ tYlqO2ov4RhHt1CLJCCU31DGYwf2R6wdw1HO9KxL.png
│  │        │  └─ wOt55j2WkWfUtIpbUiz3TRnQXBhR68peY2TOVR49.jpg
│  │        ├─ cv
│  │        │  └─ hDwdWVzz9J1Qpu1NWlO6MrE4xjQ6zFucxwil13YP.pdf
│  │        ├─ education
│  │        │  ├─ DipmA1sGUu1dTZ5RUTifFzrHFOmn736f7Vb8gdbQ.png
│  │        │  ├─ DOd5FiQwfafA8WlBQkqdATGUf8yZQdaPHLzWzVco.png
│  │        │  ├─ hQ95bf51z3M9CAmnzLKnTpBKsxgd61V0SooBkJx8.png
│  │        │  ├─ WPSmx4SOeJqIJCovkPkUjU8FYwFQuXynDG534bIV.jpg
│  │        │  └─ ZfNYxOETKyJSDkF2vxWnSnq1pAjIN1WhBe9u1T1w.jpg
│  │        ├─ experiences
│  │        │  └─ JiXHI0UG5TickTWLvEOYgmbAlYITX0nKtkXnltEW.png
│  │        ├─ icons
│  │        │  ├─ 0ASTyFQWaknPIpfnCTx3MzZj9whW8Z0pGd79ZYiT.png
│  │        │  └─ 9S9f7F2MKbcolLlMpasMpzr1FXKkw7EbEHBA1ZKK.png
│  │        ├─ products
│  │        │  └─ 2rtfc5C8A6xhEuUMH0YhWHqFrGAQd5D8qU3X5Tdj.png
│  │        ├─ profile
│  │        │  ├─ 1QWq5uuHuuiuNl2AAU1w8S3GjbzSPqudULmD0MEw.jpg
│  │        │  └─ 6UQI56JHrTsefrL8qY5BDkbiFpoBDuCMNF601c2K.jpg
│  │        ├─ projects
│  │        │  └─ dBKZe2yBVyueDC0rM5NAzoCHih8TNhHDEax3bjKV.png
│  │        └─ tool-icons
│  │           ├─ JcbEcf95F3raLyxJ1iDEgKS3XCrVntwK4eNcSlAI.png
│  │           ├─ kEGmAvHxQ2tDIGYjH9PS6WWkXPVxrYLX6qrhbYsW.png
│  │           └─ oZdOnNWB8exue0sEc04DvSx3fSK1nM2deHC5E0mx.png
│  ├─ framework
│  │  ├─ cache
│  │  │  └─ data
│  │  ├─ sessions
│  │  │  ├─ 0pOBbxdL7zbQcsI6Rfd2moK7BbCS9le9UiFWVSYP
│  │  │  ├─ 0xKi7DtVdZdi1K3hRK1JgYUmQqMPMggn53S2QdGK
│  │  │  ├─ 0ZeO7Iz5MxahSkPXjziCSAMmkMdeXBNF5eVUL6VC
│  │  │  ├─ 1gtMsCC1A4NswFTJCpvtfWSS8wsCGfV5VQZYKxLj
│  │  │  ├─ 3r93I0AdDYCZBQtVIHZGMX8lSGfqGJw26pZgs2Kj
│  │  │  ├─ 3Rqs1rILtDkBnO3luTfNcYvxHM84nadIyW5R6N0y
│  │  │  ├─ 5WzAtZ2YkjzhsdVXv4iXVZ72Wp71dn2BMNTZciOd
│  │  │  ├─ 8xypbu5zweSDusTaRgFRdDlSho7Q4t2579vPU71L
│  │  │  ├─ 9bQchLNKjkb47f4ae1dkEC9gd9rp1CQeWJG5GXMa
│  │  │  ├─ 9mm7RTClTHBOiih9yhE6RXSKsr8f90UparmaoUNu
│  │  │  ├─ A0wKkXAIMrDrYXdCgPPBzS5DXdIxbvq4U90WH4HY
│  │  │  ├─ a8JzrUpTbfrO6wCMN7Y8CKCR2RNsZNRoxzZ8mjQp
│  │  │  ├─ AlJ9lbjM7yppEeNbN29w3gbmoJ0uZ7q8SbNtgqLj
│  │  │  ├─ Amap57oh45l4oJBGsw6ZQtXTzBV06yVxZApUZchY
│  │  │  ├─ aSzlrx6nJqBdrBFyObvPnRrbaC4h4B0WsmZ4tCuO
│  │  │  ├─ AWf5GXahwDckRPSuoiM0mFhGeB81QUnB3Nx5o8hS
│  │  │  ├─ B06yimbFFdvlQGTWMKYG0sSgckU94T0FoMSSOeJi
│  │  │  ├─ bTizHS3eEeiQJ9S9hN8nGwyv9cnuXyXP0NxYmIA1
│  │  │  ├─ c0JQ13o7wS64CUuVte2VnlyiUcFh101Y2eOEraxp
│  │  │  ├─ cA2HlNTeOId1GZ8OGLRfeK80WFQPtLwtu5ui2d4b
│  │  │  ├─ cYpT785RyVYiesKFPphM2fWDjbBaqwxbMegXpJTg
│  │  │  ├─ CZrrUJeOflum73NsooLahr6HObyMcTFVsJKFdyx8
│  │  │  ├─ DDAuxKDieJN6I8t7VtR6Vw8tS8ZJfUptV1zjdL2R
│  │  │  ├─ DJqueZ381wTfjxotZyhr2m1PxZ2u9oWlvYJhdOwx
│  │  │  ├─ dsvepCm38cMcOLtbezbNLNMHfjAb4ss8qioEiRyf
│  │  │  ├─ dWvHgYLg1zHferWHa3Aa7wjfrhV6AnptPZd6wKUV
│  │  │  ├─ DYie6I34LzVaYh8aPGcl8LEKKwBMcnPLvXt7xkuZ
│  │  │  ├─ EG0Cjz8UZe0B3mXBKtYxUtb0lO1NZiK0sfskIhhp
│  │  │  ├─ embeSvrcsaPRKNwlHN4EJsAqXhPC44n9CM3Vnepv
│  │  │  ├─ EO97p4VVcfXuCdK2dfRM114s5ofSOLLmajAvyHOc
│  │  │  ├─ ESZbtQpDJl4T4MPvkULjh7gfn9BiAreP3iquLuY7
│  │  │  ├─ FIJREOG6OOk761ecTtBeLbsNTlL8KRQ54w584nCn
│  │  │  ├─ FtBTaC1F36QQZwmiJ3drPFfuq8OWDneHUSQS3kpS
│  │  │  ├─ HEpC1CzCHNdHO4fWfc7Sk0k0w9mBxgpGAD6n62Zy
│  │  │  ├─ hHEsyuBX7mgaDtyZZUaNXotzQ6IycW09w9CI9Yx7
│  │  │  ├─ HkOXCcQCE04s1mLisohgbYGpmBTN1bWXrml8qRV7
│  │  │  ├─ Hl98aaZfoRATxsTFRHXtZkcUihYxWHxj990i8gU4
│  │  │  ├─ I76cwBWmbNJAIqjVo4G2eCrEw2G6kvRc7hMItqdV
│  │  │  ├─ ibzXHVIy887tgtHqLAhP6pAFvuDKzwsq6lof9WNU
│  │  │  ├─ ik6bMqUapiN4OcdllCVMIe3jfO3cYWn30iCSoByb
│  │  │  ├─ JBUeS8yLoXSZ2D4kSP5pJUhlfrDOOIJSkQ9mMv7v
│  │  │  ├─ JFGdTGVpJqhl3WPYYdlMOunIaQihGgcYm6CBMwyH
│  │  │  ├─ JqBCFEzTxBoiqGGiW8cZUWhDXtW3hpbZpGmwdoVf
│  │  │  ├─ k0ydjUVFWNSkOoRsa487EOa2ZeD7YfKj32LRtOo6
│  │  │  ├─ K4yYhPpp53CGHIkmxFgriJyJ7NImJRBiCmmfuHSF
│  │  │  ├─ k6fmkwy1ikDqvi2dA8T6D9FkysG75CrQDNw5HHRh
│  │  │  ├─ kKEXZqkUzNg6UgyksRSMSPMenjKD1VsJIne9s5Lt
│  │  │  ├─ KVK6P0BGdyC3VK69vCn0mM0mePSyyeqhasPd4KoX
│  │  │  ├─ KyFI0pSzwyNxNl9zQP5XyasAHlVkHCMnhGWzYKJp
│  │  │  ├─ Kz5DwI71c0AB8bqSivmtaU9HqZLM3GVSP8KeZ4yK
│  │  │  ├─ l5PQr5yDem9Ob9Skg0UqNIzJtSSjhserKlwKuExz
│  │  │  ├─ LCBUEYmvvyVNADo1tWZCot7c7rrK4fOVRLmcuR84
│  │  │  ├─ lcEU0vR0Z6cGVXQ280jh1r8aIWkF84fzomJMUwIC
│  │  │  ├─ LK29iOuLkfPRQqWvBAc4qDeYE2fr2n65TFsev4SC
│  │  │  ├─ lS4cPsiJN4evz3mNgpvXK7IgxuMMYzyabrE2jiPt
│  │  │  ├─ lTmv4JBLiTQ6qOq7AggmBf1vtFhTA4rxaYrMiF4U
│  │  │  ├─ MI1UkQgjQxDZ2vPQCJIl89Y8B5FWfGB6iVc9NQRM
│  │  │  ├─ mr69VXHE9o6XkBRm3sU2P3oYFYgVPmmMhshXDZRq
│  │  │  ├─ N9NaSA9MqthP3xepmZ6ZUrc1gdM65KZmkvusWyAe
│  │  │  ├─ o93KPjvGVHg7lQu9Y2Q0JCxBtoc9vAXXU1d23PnU
│  │  │  ├─ p3FAdvoPWyIats20t25QHllpE8vKJ1loNx7jkhaD
│  │  │  ├─ pLQnT9kj5nY4IBZKSqajDpi7XNEbm02oU3BaUHoz
│  │  │  ├─ QLG7ZIpHMstfBskY620vQQw8cg1McvVJdFArjgr0
│  │  │  ├─ qMpIynZYc0pOZshNGBf4VYOFgYHOfANWk2EEmeQB
│  │  │  ├─ QnvXmJPjGjhvWlMa4LWKbijHt9AK1Niu9W7ciy8o
│  │  │  ├─ QONEra4c1tPsZLBLyO3righrEuW86MkawSGZ49g1
│  │  │  ├─ QsaOuDiSNUv2BYiMHTTUiEu6957wikLXRZvcjSOl
│  │  │  ├─ qYF7ttXPEdjH23515nLnRbo1CwlGOo6h1IqjOlEl
│  │  │  ├─ RbhSfBzBY3qw9HAWOcElnY2sK0bOVN9PED2mMq5t
│  │  │  ├─ REsiva8I07sP1PclY4itP19sdTFC9kJUFiiHejOf
│  │  │  ├─ rnQmQnAqljsJmyJEOV2I1VWFCfz7xG53ji0K1IVH
│  │  │  ├─ rUiry9PGOLvRFj9pHIAPY9coaqkonK71V3720DDj
│  │  │  ├─ sUGKoSFDkaWepIYHg3CQKVwIacOx5wov1gKPeNmb
│  │  │  ├─ swl0UggYl5mCBkzyW9xpYLR4bii5mmUDrmHxTrxG
│  │  │  ├─ SZCdk4ArcCdjE3ngE16B41W9m5tGpbzyqsxHSkrV
│  │  │  ├─ t7kPgHgb5MvkXAjFLakGIW3UFTjyMhbdrw9ndpYg
│  │  │  ├─ TBrxRfWy9NxFBuTHc9oVNpLNk8Pe72vSE5aaRlOU
│  │  │  ├─ TiuexKLDYNrP7NLEhkOZ6puzrm1XnYrzm4gR6zOY
│  │  │  ├─ tv4dXIhGco1VrmAnHYokzylAu0VbACLCdy7Ua3jN
│  │  │  ├─ TvYOY5HpA277FLitYwRTlCz4NrBVV2jrIyPAKT5V
│  │  │  ├─ U2LOAOzn2l3EGFX7xZ9HyIwkL8sW5asaMovTpGMW
│  │  │  ├─ ucjCTBlhufeLguO7d94KjOCq56YCPJSZMI5KNjAN
│  │  │  ├─ ugKA90bauRh9UGTgttFpnpToC5TgHVZC8KvxaN22
│  │  │  ├─ UYNHE8qr8drM37mD7XjSyzQextZTO0bW1uc71fVV
│  │  │  ├─ vaRyQ1AeoL8Msb9jsgQKZEkxt6U4NNdFqfdWNHLy
│  │  │  ├─ VMgkrPLe8c9lVktMl0AIPa5P7EdB6Enm8fcNSzlh
│  │  │  ├─ vONa2ue7NoDEUxYGYzj52DC6mvgRodet5mdCe8LV
│  │  │  ├─ W7yrgsts50XYwboWNOHPlxm7eAY6wesJHhGyAGw9
│  │  │  ├─ wmmYN5mX8Itly0Us0WUeszy9bMCgWLurfmJjgTu7
│  │  │  ├─ wWeMef8g9uR5UbYIN7jOiyMjJjAH37sgAH9H4ESy
│  │  │  ├─ WWqvtPG8oaehRH7L9HFrki5NPTzdbzqCJ1s4G9OF
│  │  │  ├─ xUWwv9FSHZU4Fb2wM6zza4pjsF5OKRVpfbvs4zq8
│  │  │  ├─ ZCR2NL2yQdjmowFnbeym31jzJW5ci774tqoTIQjz
│  │  │  ├─ ZDQwOgcL0u8gAkcWwpyMw7zjkpvYTJRkbBuDN8MS
│  │  │  ├─ ZJdKoOpUTaWPRsJmg2xBKJjO4blW3zRaaXW3NQib
│  │  │  ├─ zmuqOblIKGg6jZSiTLWguHFkjLHy6d57lS7DltX8
│  │  │  ├─ zvWJi5bbknVYQdNlsvjifH758B1liRjj39HPhXMG
│  │  │  └─ ZZ3mqVAUYrse2NNRnJ4gL249qu0KHXFoLIiwJ3IN
│  │  ├─ testing
│  │  └─ views
│  │     ├─ 0c0e003c23d076ab3a07124dd192f49e.php
│  │     ├─ 151243d29c021f98e2154427e726dbcd.php
│  │     ├─ 2c759689290f7d2f34e1a0f7337001de.php
│  │     ├─ 30879b098940dc65ee7ee3fa0970858d.php
│  │     ├─ 3cdb733f39d43b5c6b11447d24599491.php
│  │     ├─ 3ffecedfb8e8d98d7c4e7be7c88618d7.php
│  │     ├─ 404ebc667116ed3fe3652757017c28bf.php
│  │     ├─ 52e99af05507e1c2ead7c6a6712cf835.php
│  │     ├─ 5311c9aaad963115108f108d3b7b9b62.php
│  │     ├─ 536eaf0630e7f28a8082f0d69ebd6707.php
│  │     ├─ 55235dae25c1fc804f5fea57c3eb507e.php
│  │     ├─ 597010ea672daefda599da55cdf6a572.php
│  │     ├─ 602916b6b7438923168a1f2fdcc7a741.php
│  │     ├─ 6121c434bdf3dc9c090cbe01d6175b1f.php
│  │     ├─ 6324447fc4f47a997eaf2c96b2995b1e.php
│  │     ├─ 64270f7503b34bac1e38229d0f6e1f94.php
│  │     ├─ 650a0b68aafd385f08ed485480ed76a9.php
│  │     ├─ 7945819a2fce50f7eab8555372fa523f.php
│  │     ├─ 7cfaaa4fa5af896e84882a305e7851cb.php
│  │     ├─ 7e4a82035740386a42ea8631c46e2439.php
│  │     ├─ 7fa2d8e818a21cc330ee54f9cd974fa6.php
│  │     ├─ 84aa93d8f226568d449edf73fa1902a0.php
│  │     ├─ 91cfc8b5983ad33bf3f7d353b5d94b7a.php
│  │     ├─ 941c83ce44b2ecdb5b4d930f25522779.php
│  │     ├─ 9a0ddf75c7d706bdf593087c5f83e17c.php
│  │     ├─ 9b059da63ce2788229b674da708e355e.php
│  │     ├─ a0d5cb0acd512039dd6f6ba250151365.php
│  │     ├─ a8c50fc88f38ad0c65e998a26355549a.php
│  │     ├─ b4d12d8b52fe4854e8a2857563d61f0c.php
│  │     ├─ bb68f7e79722e65e9113cf04d52ea4ef.php
│  │     ├─ c70ad9a958c9718937a54683f7c911fa.php
│  │     ├─ cb0adc423abf32b814afdb157f437f39.php
│  │     ├─ cd2353221662c3e3099641f4c3268d78.php
│  │     ├─ d0781f3742983f684156996f92336a48.php
│  │     ├─ dbe0d0a75f32c86cee9714572101406c.php
│  │     ├─ ddae4b3230164e8b42abfb0919bba5da.php
│  │     ├─ e282f9d09f6b348a5aa5680f8270daf2.php
│  │     ├─ ee5990edec263431c4cea7f964396207.php
│  │     ├─ f578ad02f93ef21200e4bbe4d1ef3e9b.php
│  │     ├─ f652cb6d1a5f8a78c2c1e2529603a7db.php
│  │     ├─ f986692e788d7d0220eef56f81d47a14.php
│  │     └─ fc15dd5272046bbbd51efda1d39bc857.php
│  └─ logs
├─ tests
│  ├─ Feature
│  │  └─ ExampleTest.php
│  ├─ TestCase.php
│  └─ Unit
│     └─ ExampleTest.php
└─ vite.config.js

```
