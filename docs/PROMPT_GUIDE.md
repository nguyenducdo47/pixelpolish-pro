# Hướng dẫn đặt prompt cho Agent

Tài liệu này dành cho **người giao việc** (bạn), không thay `AGENT_MAP.md` / `AGENT_GUIDE.md`. Mục tiêu: mỗi task dùng **ít token**, agent **đúng domain**, **ít quét repo**.

---

## Trước khi gửi prompt

Agent mặc định sẽ (xem `.cursor/rules/project-context.mdc`):

1. `docs/AGENT_MAP.md` → domain + file cần mở  
2. `docs/AGENT_GUIDE.md` → quy trình  
3. `docs/business/<domain>.md` → rule nghiệp vụ  
4. **Không** tự chạy test / migrate / SQL — bạn chạy lệnh agent gợi ý  

Bạn **không cần** nhắc “đọc hết source” mỗi lần. Chỉ cần **task rõ + domain + phạm vi**.

---

## Cấu trúc prompt nên có

| Phần | Bắt buộc? | Ghi chú |
|------|-----------|---------|
| **Task** (1 câu) | Có | Làm gì, kết quả cho ai |
| **Domain** | Nên có | Xem bảng domain bên dưới |
| **Surface** | Nên có | Public web / Studio / Admin / PDF / email |
| **Acceptance** | Nên có | 2–5 điều kiện “xong là đúng” |
| **Out of scope** | Tùy | Tránh agent sửa lan |
| **Bug: tái hiện** | Nếu fix bug | Bước, URL, user role, kỳ vọng vs thực tế |

### Domain gợi ý (map sang docs)

| Bạn mô tả | Domain | Doc |
|-----------|--------|-----|
| slug, publish, preview 404 | portfolio | `business/portfolio.md` |
| trang portfolio, landing, SEO | public-site | `business/public-site.md` |
| CV, PDF, template CV | cv | `business/cv.md` |
| đa ngôn ngữ, locale URL | i18n | `business/i18n.md` |
| màu, theme, giao diện site | appearance | `business/appearance.md` |
| wizard, /studio, CRUD nội dung | studio + content-sections | `business/studio.md`, `content-sections.md` |
| /admin, user, impersonate | admin | `business/admin.md` |
| login, register, reset password | auth | `business/auth.md` |
| SMTP, mail test | mail | `business/mail.md` |
| nút Dịch trong editor | translation | `business/translation.md` |

Chi tiết task → file: **`AGENT_MAP.md` §4**.

---

## Mẫu prompt chung (copy-paste)

```text
Task: [một câu]

Domain: [portfolio | public-site | cv | i18n | appearance | studio | admin | auth | mail | translation | content-sections]

Surface: [Public Inertia | Filament Studio | Filament Admin | CV Blade PDF | cả hai...]

Acceptance:
1. ...
2. ...

Out of scope: ...

Ghi chú: Đọc AGENT_MAP + business doc domain trước. Không chạy test/migrate/SQL — đưa lệnh cho tôi chạy.
```

---

## Theo loại công việc

### 1. Chức năng mới (feature)

**Nên nói rõ:**

- Dữ liệu mới hay chỉ UI?  
- Owner nhập ở đâu (wizard / resource / page Filament)?  
- Khách/public có thấy không → gần như luôn cần **`PortfolioPresenter`** nếu lên site/CV/PDF.  
- Field đa ngôn ngữ? → domain **i18n** + `LocaleTabs`.

**Mẫu:**

```text
Task: Thêm [field/tính năng] cho portfolio.

Domain: content-sections + public-site (+ cv nếu hiện trên CV)

Surface: Studio (wizard + ManageProfile hoặc resource X), public Show.vue, [Cv.vue / pdf nếu cần]

Acceptance:
1. Owner lưu được từ Studio.
2. Guest thấy trên /{locale}/{username} [và CV/PDF nếu có].
3. Đồng bộ wizard + [resource/page] theo workflows/content-edit-sync.md.

Out of scope: không đổi theme, mail, admin users.

Không refactor ngoài phạm vi. Cuối task đề xuất cập nhật docs/business nếu có rule mới.
```

**Source of truth thường gặp:** `PortfolioWizardSync`, `PortfolioPresenter`, migration + model, Filament form tương ứng.

---

### 2. Sửa bug

**Nên nói rõ:**

- URL đầy đủ (kèm locale nếu public).  
- Tài khoản: guest / owner / admin / đang impersonate.  
- Đã publish chưa.  
- Kỳ vọng vs thực tế (1–2 câu).

**Mẫu:**

```text
Task: Fix [mô tả ngắn].

Domain: [domain]

Tái hiện:
1. User: [guest | owner | admin]
2. URL: ...
3. Thao tác: ...
4. Kỳ vọng: ... | Thực tế: ...

Gợi ý inspect (có thể bỏ qua nếu sai): AGENT_MAP §4 hàng "[...]"

Out of scope: không đổi behavior ngoài bug.

Không chạy test — sau khi fix gợi ý: php artisan test --filter=...
```

---

### 3. Chỉ đổi giao diện public (Vue / CSS)

```text
Task: [mô tả UI] trên trang portfolio / CV / landing.

Domain: public-site (+ appearance nếu màu/layout/particles)

Surface: resources/js/Pages/... hoặc PublicLayout.vue

Acceptance:
1. ...
2. Không đổi shape dữ liệu từ backend trừ khi tôi yêu cầu.

Out of scope: Filament Studio, PDF [hoặc ghi rõ nếu PDF cũng cần khớp].
```

Nếu cần prop mới từ backend → nhắc agent mở **`PortfolioPresenter`**, không chỉ sửa Vue.

---

### 4. Theme / appearance

```text
Task: [preset, màu, layout, import theme, ...]

Domain: appearance (+ cv nếu đổi cv_layout)

Surface: ManageAppearance / ThemeResource (admin) / public site

Acceptance:
1. ...
2. Nhớ hai khái niệm: default_theme (sáng/tối) vs AppearanceTheme (preset).

Out of scope: ...
```

---

### 5. Admin / platform (locale, mail, users)

```text
Task: [ví dụ: thêm locale, sửa SMTP, impersonate]

Domain: admin | mail | i18n

Surface: Filament /admin only [hoặc rõ Studio nếu liên quan owner]

Acceptance:
1. Chỉ admin được phép (theo business/admin.md hoặc mail.md).
2. ...

Out of scope: public site trừ khi có liên quan catalog locale.
```

---

### 6. Chỉ tài liệu / hiểu nghiệp vụ (không code)

```text
Task: Giải thích [feature X] hoạt động thế nào / cập nhật docs.

Domain: ...

Không sửa business logic. Chỉ đọc docs + source of truth; nếu doc sai thì đề xuất sửa file docs/ cụ thể.

Out of scope: refactor code.
```

---

### 7. Task lớn — chia phase

```text
Phase 1 (chỉ phân tích): Đọc AGENT_MAP + business doc, liệt kê file cần sửa và rủi ro. Không code.

Phase 2 (implement): [paste lại task + "làm theo danh sách Phase 1 đã duyệt"]
```

Gửi **hai message** hoặc một message có hai phase rõ ràng; duyệt Phase 1 trước khi Phase 2.

---

## Ví dụ cụ thể (Portfotilo)

Các prompt dưới đây **điền sẵn** theo codebase thật — bạn chỉ sửa tên field / URL / mô tả cho task của mình.

### Feature: field mới đa ngôn ngữ trên project

```text
Task: Thêm field "client_name" (JSON locale) cho từng project, hiển thị dưới subtitle trên trang public.

Domain: content-sections + i18n + public-site

Surface: Filament ProjectResource + SetupWizard projects step, PortfolioPresenter, Show.vue

Acceptance:
1. Owner nhập VI/EN qua LocaleTabs trong Studio (resource + wizard).
2. Guest thấy trên /en/{slug} và /vi/{slug} khi có nội dung locale đó.
3. Không hiện trên CV/PDF lần này.

Out of scope: migration seed demo, theme, is_featured filter.

Đọc workflows/content-edit-sync.md. Tạo migration, không chạy migrate — tôi chạy sau.
```

### Feature: toggle section CV + PDF

```text
Task: Thêm toggle "show_social_links" trong CV settings; khi bật, hiện block social trên Cv.vue và cv.pdf.

Domain: cv + content-sections

Surface: ManageCv, wizard cv step, cv_settings migration, PortfolioPresenter (cv.settings), Cv.vue, resources/views/cv/

Acceptance:
1. Mặc định false cho portfolio mới (migration default).
2. PDF và web CV cùng logic bật/tắt.
3. Wizard và ManageCv đều lưu được.

Out of scope: đổi layout template modern/classic.

Source of truth: CvSetting + presenter cv.settings — xem business/cv.md.
```

### Feature: reserved username mới

```text
Task: Cấm đăng ký username "api" và "health".

Domain: auth + portfolio

Surface: RegisterRequest (Rule::notIn)

Acceptance:
1. /register trả validation error nếu username là api hoặc health.
2. Không ảnh hưởng user đã tồn tại.

Out of scope: đổi slug portfolio hiện có.

Gợi ý test: php artisan test --filter=AuthTest (tôi tự chạy).
```

---

### Bug: guest 404 dù owner đã publish

```text
Task: Fix guest không xem được portfolio dù Studio bật "Publish".

Domain: portfolio + public-site

Tái hiện:
1. User: guest (logout)
2. URL: http://portfotilo.test/vi/nguyenducdo
3. Studio: is_published = true, slug = nguyenducdo
4. Kỳ vọng: 200 Show page | Thực tế: 404

Out of scope: đổi rule preview owner/admin.

Inspect theo AGENT_MAP §4 "Publish / unpublish / preview 404".
Không chạy test — gợi ý lệnh verify sau fix.
```

### Bug: locale URL 404

```text
Task: /de/{username} luôn 404 dù admin đã bật locale "de" trong Locales.

Domain: i18n

Tái hiện:
1. Admin → Locales → de is_enabled = true
2. URL: /de/myuser → 404
3. /vi/myuser và /en/myuser OK

Out of scope: thêm locale mới (de đã có trong DB).

Gợi ý: SetLocale, LocaleCatalog::isEnabled, cache LocaleCatalog::forget sau save Locale.
```

### Bug: avatar wizard không lên preview site

```text
Task: Upload avatar ở wizard step Profile, preview iframe vẫn không có ảnh cho đến khi Finish.

Domain: studio

Tái hiện:
1. Owner, /studio/setup, step Profile, chọn ảnh mới
2. Mở preview site (tab preview step hoặc link)
3. Kỳ vọng: thấy avatar tạm | Thực tế: avatar cũ hoặc trống

Out of scope: ManageProfile upload (trừ khi cùng root cause).

Inspect: WizardPendingAvatar, previewUrls trong SetupWizard, PortfolioPresenter urlFor.
```

### Bug: PDF không có avatar, web CV có

```text
Task: /vi/{slug}/cv.pdf thiếu avatar; /vi/{slug}/cv hiển thị avatar bình thường.

Domain: cv

Tái hiện:
1. Guest, portfolio published, avatar_path file tồn tại trên public disk
2. Web CV: có img | PDF: header không ảnh

Out of scope: đổi template CV.

Inspect: PortfolioController::avatarToBase64, pdf.blade.php header điều kiện show_avatar.
```

---

### UI: chỉ Vue, không backend

```text
Task: Trên Show.vue, section Projects: thêm badge "Featured" cạnh title nếu project.is_featured === true (data đã có trong props).

Domain: public-site

Surface: resources/js/Pages/Portfolio/Show.vue only

Acceptance:
1. Mobile + desktop không vỡ layout.
2. Không sửa PortfolioPresenter (field đã map).

Out of scope: lọc bỏ project không featured; Filament; PDF.
```

### UI: dark mode public

```text
Task: Trang portfolio tôn trọng default_theme = dark ngay lần load đầu (tránh flash sáng).

Domain: public-site + appearance (default_theme vs preset)

Surface: PublicLayout.vue, prop theme từ Inertia

Acceptance:
1. User portfolio default_theme dark → nền tối trước paint.
2. system vẫn theo OS.

Out of scope: đổi màu preset AppearanceTheme.
```

---

### Theme: đổi cv_layout khi save appearance

```text
Task: Khi owner đổi cv_layout trong Manage Appearance, trang /cv và PDF phải dùng layout mới mà không cần vào Manage CV riêng.

Domain: appearance + cv

Surface: ManageAppearance, AppearanceTheme::apply / syncCvLayout

Acceptance:
1. Save appearance → cv_settings.template đồng bộ (đúng behavior hiện tại hoặc fix nếu lệch).
2. Mô tả ngắn trong reply nếu đúng là bug.

Out of scope: thêm template thứ tư.
```

---

### Admin: SMTP + test mail

```text
Task: Sau khi admin Save mail SMTP, gửi "Send test" phải dùng from_name vừa nhập (kể cả chưa reload trang).

Domain: mail

Surface: ManageMail.php, MailSetting

Tái hiện (nếu bug): đổi from_name → Send test → email vẫn tên cũ

Out of scope: queue, notification khác reset password.

Đọc business/mail.md trước.
```

### Admin: impersonation

```text
Task: Admin impersonate user A, sửa project trong Studio, Leave impersonation — admin không mất quyền vào /admin/users.

Domain: admin

Surface: ImpersonationController, User canAccessPanel

Acceptance:
1. Luồng enter → edit → leave như docs/workflows/admin-impersonation.md.
2. Fix nếu leave redirect sai hoặc admin panel 403 sau leave.

Out of scope: audit log impersonation.
```

---

### i18n: thêm locale catalog

```text
Task: Admin thêm locale "ja" (Japanese); URL /ja/{username} hoạt động khi enabled; LocaleTabs có tab ja.

Domain: i18n + admin

Surface: LocaleResource, LocaleSeeder pattern, LocaleCatalog, SetLocale routes

Acceptance:
1. is_enabled false → /ja/... 404.
2. available_locales trên site có link ja khi enabled.

Out of scope: dịch sẵn nội dung portfolio demo.

Chỉ tạo migration/seed nếu cần — tôi chạy migrate/seed.
```

---

### Docs-only

```text
Task: Cập nhật docs/business/studio.md — ghi rõ previewUrls() gọi saveAll trước khi build link (side effect).

Domain: studio

Không sửa PHP. Chỉ sửa docs nếu xác nhận đúng trong SetupWizard.php.

Out of scope: refactor wizard.
```

---

### Task lớn (2 phase) — ví dụ “Certifications section”

**Message 1:**

```text
Phase 1 only: Muốn thêm section "Chứng chỉ" (repeater: tên, tổ chức, năm, URL) giống education.

Đọc AGENT_MAP + content-sections + content-edit-sync.

Output: danh sách file cần tạo/sửa (migration, model, resource, wizard, presenter, Vue, CV?), thứ tự implement, rủi ro. Không code.
```

**Message 2 (sau khi duyệt):**

```text
Phase 2: Implement certifications theo plan Phase 1 đã duyệt (paste plan).

Acceptance: [copy từ plan]
Out of scope: [copy từ plan]
Không chạy migrate/test — tôi chạy.
```

---

### Prompt kém → prompt tốt (cùng nhu cầu)

| ❌ Kém (tốn token, dễ lạc) | ✅ Tốt |
|---------------------------|--------|
| “Sửa CV cho đẹp hơn” | “Task: CV web — tăng spacing section education trên Cv.vue; không đổi PDF. Domain: cv. Surface: Cv.vue only.” |
| “Làm đa ngôn ngữ cho skills” | “Task: Skill name hiện string — giữ vậy; chỉ thêm mô tả skill đa locale (đã có). Fix bug mô tả EN trống trên /en/…. Domain: i18n + content-sections.” |
| “Fix login” | “Task: Admin login redirect về / thay vì /admin. Domain: auth. Tái hiện: is_admin=true, POST /login → …” |
| “Refactor toàn bộ Filament” | “Out of scope: refactor. Task: chỉ thêm field X vào ProjectResource form theo pattern LocaleTabs hiện có.” |

---

## Những câu **không cần** lặp lại

- “Đọc toàn bộ project / scan hết app”  
- “Nhớ đây là Laravel Filament Inertia” (đã có trong rules)  
- Dán nguyên file dài khi chưa xác định vị trí — chỉ cần tên feature + route + lỗi  

## Những câu **nên** thêm khi agent hay làm sai

- “Public data chỉ qua `PortfolioPresenter`.”  
- “Wizard và resource cùng field → sync theo `content-edit-sync.md`.”  
- “Không commit trừ khi tôi bảo.”  
- “Không chạy migrate — chỉ tạo migration file, tôi chạy `php artisan migrate`.”  

---

## Sau khi agent xong

Bạn có thể kiểm tra nhanh:

1. Có đúng domain doc / SoT trong `README.md` không?  
2. Bug fix có đụng đúng gate (publish, locale, scope Filament)?  
3. Chạy lệnh test agent gợi ý (nếu có).  
4. Nếu rule mới lặp lại → nhắc agent cập nhật `docs/business/*.md`.

---

## Liên kết

| File | Vai trò |
|------|---------|
| [AGENT_MAP.md](AGENT_MAP.md) | Bản đồ domain + task → file |
| [AGENT_GUIDE.md](AGENT_GUIDE.md) | Quy trình agent |
| [README.md](README.md) | Index + Source of Truth |
| `.cursor/rules/user-run-commands.mdc` | Test/DB do bạn chạy |
