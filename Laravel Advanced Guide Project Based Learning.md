# Laravel Advanced Guide: Project Based Learning

*Beginner ke liye, Hinglish me. Har cheez: kya hai, kyun use karein, fayda, kab kaam aati hai, aur easy example. Ek project (EventHub) banate hue sab seekhna hai.*

## Doc kaise use karein

1. **Part 1** me project aur roadmap samjho.
2. **Part 2** ka prompt kisi bhi AI me paste karo aur phase by phase project banao.
3. **Part 3** me har concept easy language me hai. Jab bhi koi cheez samajh na aaye, yahan dekho.

Sabse zaroori rule: har concept pehle **bug ke saath** dekho (slow query, overbooking, data leak), phir fix karo. Bug dekhe bina fix samajh nahi aata.

## Part 1: Project (EventHub)

EventHub ek event booking platform hai. Organizers events aur ticket types banate hain (seats limited), users tickets book karte hain, booking ke baad PDF ticket email hota hai.

**Stack:** Laravel 11, MySQL, Blade + Tailwind (Breeze), Pest, queue driver = database.

**Ye project kyun:** seats limited hain to race condition aayegi, booking me transaction chahiye, PDF aur email queue me jayenge, har organizer ka data alag hona chahiye, lakhon bookings export karni hongi, aur dashboard cache maangta hai. Matlab saare advanced topics natural tareeke se aa jate hain.

| Feature | Kaun sa concept |
| --- | --- |
| Events list (organizer + category ke saath) | N+1, indexes |
| Last seat ke liye 2 log book karein | Transactions, locking |
| Organizer ka private data | Multi-tenancy, policies, IDOR |
| Ticket PDF + email | Queues, retries, idempotent jobs |
| 1 lakh bookings CSV export | chunkById, lazy, batches |
| Event pe comments aur likes | Polymorphic relations |
| Cancel hua event wapas laana | Soft deletes |
| Dashboard stats | Cache, tags |
| Event se 1 din pehle reminder | Scheduler |
| Poora project | Tests, fakes, action classes, events |

### Roadmap (8 phases)

| Phase | Kya banana hai | Concepts (Part 3 ke number) |
| --- | --- | --- |
| 1 | Setup, DB design, factories, 50,000 bookings ka seeder | 2, 8, 9 |
| 2 | Events list aur performance | 1, 2, 6 |
| 3 | Booking engine | 4, 5, 21 |
| 4 | Security aur organizer isolation | 10 se 14 |
| 5 | Ticket PDF + email background me | 15 se 19 |
| 6 | Bookings export aur dashboard | 3, 18, 26 |
| 7 | Comments, payment gateway, events | 7, 20, 22, 23 |
| 8 | Tests, scheduler, deploy | 24, 25, 27, 28 |

## Part 2: Prompt (copy-paste karo)

Ek hi chat me phase by phase chalo. Har phase ke baad `next` likho.

```
Tum meri Laravel mentor ho. Main beginner hoon aur Hinglish me samajhna chahta hoon. Hum EventHub naam ka event booking project banayenge (Laravel 11, MySQL, Blade + Tailwind via Breeze, Pest tests, queue driver: database). Maqsad: project banate hue advanced Laravel concepts seekhna.

Project: Organizers events aur ticket types banate hain (limited seats). Users tickets book karte hain, booking ke baad PDF ticket email hota hai. Ek organizer ka data dusre organizer se alag rehna chahiye. Organizer bookings CSV export kar sake aur dashboard stats dekh sake. Events aur organizers par comments/likes bhi hon.

Roadmap (8 phases, ek time par sirf ek phase):
1. Setup + DB design (indexes, soft deletes, casts) + 50,000 bookings ka seeder
2. Events list + performance (N+1, indexes/EXPLAIN, global scopes)
3. Booking engine (transactions, locking, action class)
4. Security (IDOR, policies, rate limiting, multi-tenancy, mass assignment, signed URLs)
5. Queues (jobs, retries/backoff/timeout, idempotency, chains/batches/unique, supervisor/horizon)
6. Export + scale (chunkById/lazy, batches, cache/tags/atomic locks)
7. Architecture (service container, events/listeners/observers, middleware, polymorphic comments)
8. Tests + scheduler + deploy (Pest, fakes, scheduler, cache commands, logging)

Har phase me ye exact order follow karo:
1. CONCEPT: har naye concept ko chhote me samjhao: kya hai, kyun use karein, fayda, real-life example.
2. BUG PEHLE: pehle jaan-boojh kar galat ya slow tareeke se banwao, taaki main bug khud dekh sakun (jaise N+1 ka query count, overbooking, dusre ka data khulna). Bug reproduce karne ke steps do.
3. FIX: phir sahi tareeka do aur batao pehle wale me kya galat tha.
4. TASK: ek chhota task do jo main bina code dekhe khud karun. Atak jaun tabhi hint ya code do.
5. TEST: us phase ke liye Pest test likhwao jo bug ko pakde.
6. CHECKLIST: 3-4 sawal jinse main check karun ki mujhe samajh aaya ya nahi.
Uske baad mere next ka wait karo.

Rules:
- Poora project ek saath mat do. Ek phase, phir ruko.
- Jawab chhote aur simple rakho. Jargon aaye to turant samjhao.
- Commands (artisan, composer) exact do aur har code ke upar file path likho.
- Code complete aur ready-to-run ho, jo variable chahiye pehle se declare ho.
- Meri galti ho to seedha batao kyun galat hai.

Shuru karo: Phase 1.
```

## Part 3: Concepts (easy explanation)

Har concept me: **Kya hai**, **Kyun / fayda**, **Case** (kab use hota hai), **Example**.

### 1. N+1 Problem (Phase 2)

**Kya hai:** Ek query se list laao, phir har item ke liye alag query chale. 100 events = 1 + 100 queries. **Kyun / fayda:** Page slow hota hai aur DB pe load badhta hai. Eager loading se sirf 2 queries lagti hain. **Case:** Events list jisme har event ke saath organizer ka naam dikhana hai. **Example:** Dukaan se 100 cheezein chahiye. N+1 matlab 100 baar dukaan jaana. Eager loading matlab ek baar list dekar sab le aana.

```php
// Bura: 101 queries
$events = Event::all();
foreach ($events as $e) { echo $e->organizer->name; }

// Sahi: 2 queries
$events = Event::with('organizer')->get();
```

Tip: `AppServiceProvider` ke `boot()` me `Model::preventLazyLoading()` lagao. N+1 hote hi error dega.

### 2. Indexes aur EXPLAIN (Phase 1, 2)

**Kya hai:** Index book ke piche wale index page jaisa hai. Bina index ke DB poori table scan karta hai. **Kyun / fayda:** Search, filter aur sort fast ho jate hain. **Case:** `where('organizer_id', ...)`, slug se event dhoondhna, date se sort. **Example:** 10 lakh bookings me ek user ki bookings dhoondhna. Bina index har row check hoti hai, index ke saath seedha jump.

```php
$table->index(['organizer_id', 'starts_at']);
$table->unique('slug');
```

Check karne ke liye MySQL me `EXPLAIN select * from events where organizer_id = 5;` chalao. `type = ALL` matlab full scan (slow), `ref` matlab index use ho raha hai.

### 3. chunkById, lazy (Phase 6)

**Kya hai:** Bade data ko chhote tukdo me process karna. **Kyun / fayda:** `get()` saari rows RAM me laata hai, memory khatam hoti hai aur server crash ho sakta hai. **Case:** 1 lakh bookings ka CSV export. **Example:** Pure 1 lakh page ki kitaab ek saath uthane ki jagah 1000 page ke bunch me padhna.

```php
Booking::query()->chunkById(1000, function ($rows) {
    foreach ($rows as $b) { /* CSV me likho */ }
});

// ya generator style
foreach (Booking::lazyById(1000) as $b) { /* ... */ }
```

Update ya delete karte hue `chunk()` ki jagah `chunkById()` use karo, warna rows skip ho sakti hain.

### 4. Transactions (Phase 3)

**Kya hai:** Kai DB kaam ek packet me: ya sab hon ya koi nahi. **Kyun / fayda:** Beech me error aaye to adhoora data nahi bachta, sab rollback ho jata hai. **Case:** Booking me seat ghatao, booking banao, payment record likho. Payment record fail ho to seat bhi wapas. **Example:** ATM: paise kat gaye par cash nahi nikla, to kate hue paise wapas aane chahiye.

```php
DB::transaction(function () use ($user, $ticketType) {
    $ticketType->decrement('available');
    Booking::create(['user_id' => $user->id, 'ticket_type_id' => $ticketType->id]);
});
```

Beech me exception aaya to Laravel khud rollback kar deta hai.

### 5. Locking aur Race Condition (Phase 3)

**Kya hai:** Do users ek hi time par same record badlein to galat result aata hai (race condition). Lock ek ko rokta hai jab tak dusra kaam khatam na kare. **Kyun / fayda:** Overbooking aur galat balance se bachav. **Case:** 1 seat bachi hai, 2 log ek saath book karte hain, dono ko mil jati hai. **Example:** Train ki last seat do counters par do clerk ek saath de dein. Lock matlab ek clerk ke kaam ke time dusra ruke.

```php
DB::transaction(function () use ($id) {
    $t = TicketType::lockForUpdate()->findOrFail($id);
    if ($t->available < 1) { abort(422, 'Sold out'); }
    $t->decrement('available');
    // booking create karo
});
```

`lockForUpdate()` hamesha transaction ke andar use karo. Seekhne ka tareeka: do terminal se ek saath request bhejo, bina lock ke bug dekho, phir lock lagao.

### 6. Global Scopes (Phase 2)

**Kya hai:** Model ki har query par automatic condition. **Kyun / fayda:** Har jagah `where` likhna nahi padta, bhoolne se bug nahi aata. **Case:** Sirf published events dikhane hain. **Example:** Mall ka guard jo har gate par apne aap ID check karta hai.

```php
protected static function booted(): void
{
    static::addGlobalScope('published', fn ($q) => $q->where('status', 'published'));
}

// Kabhi hatana ho:
Event::withoutGlobalScope('published')->get();
```

### 7. Polymorphic Relations (Phase 7)

**Kya hai:** Ek table (comments) kai alag models (Event, Organizer) se jud sakti hai. **Kyun / fayda:** Har model ke liye alag comments table nahi banani padti. **Case:** Events aur organizers dono par comments aur likes. **Example:** Ek hi review register jisme likha hai kiske baare me review hai (type + id).

```php
// migration (ULID use karte ho to ulidMorphs)
$table->morphs('commentable');

// Event model
public function comments() { return $this->morphMany(Comment::class, 'commentable'); }

// Comment model
public function commentable() { return $this->morphTo(); }
```

### 8. Custom Casts aur Attribute (Phase 1)

**Kya hai:** DB ki value ko code me alag form me apne aap convert karna. **Kyun / fayda:** Conversion logic ek hi jagah rehta hai. **Case:** Price paise me store (49900) par rupee me dikhana (499). **Example:** Dukaandar hisaab paise me rakhta hai, customer ko rupee me batata hai.

```php
// Attribute class import karna mat bhoolna
protected function price(): Attribute
{
    return Attribute::make(
        get: fn ($v) => $v / 100,
        set: fn ($v) => (int) round($v * 100),
    );
}
```

Paisa hamesha integer me store karo, float me hisaab galat ho sakta hai.

### 9. Soft Deletes aur Unique Gotcha (Phase 1)

**Kya hai:** Delete par row hatti nahi, bas `deleted_at` set hota hai. **Kyun / fayda:** Galti se delete hua data wapas aa sakta hai. **Case:** Cancel hua event restore karna, purani bookings ka record rakhna. **Gotcha:** Unique column (slug, email) me soft-deleted row bhi gini jati hai, to wahi slug dobara nahi banega.

```php
$event->delete();       // soft delete
$event->restore();      // wapas
Event::withTrashed()->get();

// Validation me deleted rows ignore karo
Rule::unique('events', 'slug')->whereNull('deleted_at')
```

DB ka unique index phir bhi block karega. Simple solution: delete par slug badal do (jaise `slug-deleted-123`).

### 10. IDOR (Phase 4)

**Kya hai:** URL me ID badal ke dusre ka data dekh lena (`/bookings/21` se `/bookings/22`). **Kyun seekhna hai:** Sabse common security bug hai. **Case:** User dusre user ki booking ya ticket dekh le. **Example:** Hotel me room number badal kar kisi aur ke room me ghus jana, kyunki guard ne key check hi nahi ki.

```php
// Bura
$booking = Booking::findOrFail($id);

// Sahi: sirf apni booking
$booking = $request->user()->bookings()->findOrFail($id);
```

ULID ya UUID sirf ID chhupate hain, security nahi dete. Ownership check phir bhi zaroori hai.

### 11. Policies aur Gates (Phase 4)

**Kya hai:** Kaun kya kar sakta hai, uske rules ek class me. **Kyun / fayda:** Har controller me if-else nahi, rules ek jagah. **Case:** Sirf organizer apna event edit ya delete kare. **Example:** Office ka ID card: card se decide hota hai kaun kis floor par ja sakta hai.

```php
// php artisan make:policy EventPolicy --model=Event
public function update(User $user, Event $event): bool
{
    return $user->organizer_id === $event->organizer_id;
}

// Controller me
Gate::authorize('update', $event);
```

Blade me: `@can('update', $event) ... @endcan`

### 12. Rate Limiting (Phase 4)

**Kya hai:** Ek user ya IP kitni baar request bhej sakta hai, uski limit. **Kyun / fayda:** Spam, brute force aur abuse se bachav. **Case:** Login, booking, OTP. **Example:** Canteen me ek aadmi ko ek baar me sirf 5 samose.

```php
// AppServiceProvider::boot
RateLimiter::for('booking', fn ($r) => Limit::perMinute(5)->by($r->user()?->id ?: $r->ip()));

Route::post('/book', BookController::class)->middleware('throttle:booking');
```

### 13. Multi-tenancy (Phase 4)

**Kya hai:** Ek app, kai organizers (tenants), aur har ka data alag. **Kyun / fayda:** Ek organizer dusre ka data kabhi na dekh paye. **Case:** Har organizer sirf apne events aur bookings dekhe. **Example:** Ek apartment building jisme har flat ka apna lock hai.

Simple tareeka: har table me `organizer_id` aur ek global scope.

```php
trait BelongsToOrganizer
{
    protected static function bootBelongsToOrganizer(): void
    {
        static::addGlobalScope('organizer', function ($q) {
            if (auth()->check()) { $q->where('organizer_id', auth()->user()->organizer_id); }
        });
        static::creating(function ($m) {
            $m->organizer_id ??= auth()->user()?->organizer_id;
        });
    }
}
```

Dhyaan rakho: queue jobs aur scheduler me `auth()` user nahi hota. Wahan scope ko bypass karo ya `organizer_id` khud do.

### 14. Mass Assignment aur Signed URLs (Phase 4)

**Mass assignment:** `Event::create($request->all())` me user extra field (jaise `organizer_id`, `is_admin`) bhej sakta hai. Hamesha `$request->validated()` use karo aur model me `$fillable` rakho. **Signed URL:** Temporary aur chhed-chhad-proof link, bina login ke chalta hai. Case: ticket download link sirf 30 minute ke liye.

```php
$url = URL::temporarySignedRoute('ticket.download', now()->addMinutes(30), ['booking' => $booking->id]);

Route::get('/ticket/{booking}', DownloadTicket::class)->name('ticket.download')->middleware('signed');
```

### 15. Queues aur Jobs (Phase 5)

**Kya hai:** Bhaari ya slow kaam response ke baad background me chalna. **Kyun / fayda:** User ko wait nahi karna padta, page fast rehta hai. **Case:** Booking ke baad PDF ticket banana aur email bhejna. **Example:** Restaurant me waiter order lekar turant chala jata hai, khana kitchen me baad me banta hai.

```php
// php artisan make:job SendTicketJob
SendTicketJob::dispatch($booking);

// worker chalao (alag terminal me)
// php artisan queue:work
```

Shuru me `QUEUE_CONNECTION=database`, baad me Redis.

### 16. Retries, Backoff, Timeout, failed() (Phase 5)

**Kya hai:** Job fail ho to kitni baar try ho, kitne gap par, aur kitni der tak chale. **Kyun / fayda:** Temporary error (mail server down) par job apne aap dobara chalti hai. **Case:** Email ya PDF service kabhi kabhi fail hoti hai.

```php
class SendTicketJob implements ShouldQueue
{
    public int $tries = 3;
    public int $timeout = 60;

    public function backoff(): array { return [10, 60, 300]; } // seconds

    public function failed(Throwable $e): void { /* log ya alert */ }
}
```

`config/queue.php` ka `retry_after` hamesha `timeout` se bada rakho, warna job do baar chal jati hai. Failed jobs dekhne ke liye `php artisan queue:failed`, dobara chalane ke liye `php artisan queue:retry all`.

### 17. Idempotent Jobs (Phase 5)

**Kya hai:** Job do baar chale to bhi result same rahe. **Kyun / fayda:** Retry ya duplicate se double email ya double charge nahi hota. **Example:** Lift ka button 5 baar dabane se lift 5 baar nahi aati.

```php
public function handle(): void
{
    if ($this->booking->ticket_sent_at) { return; } // pehle hi bhej chuke

    // PDF banao, mail bhejo

    $this->booking->update(['ticket_sent_at' => now()]);
}
```

### 18. Chains, Batches, ShouldBeUnique (Phase 5, 6)

**Chain:** Ek job ke baad dusri. **Batch:** Bahut saari jobs ka group, jisme progress dikhta hai aur sab khatam hone par kaam chalta hai. **ShouldBeUnique:** Wahi job queue me do baar nahi jayegi. **Case:** Export ke 100 chunk jobs batch me, sab khatam hon to ZIP banao aur email bhejo.

```php
Bus::chain([new GeneratePdf($booking), new SendTicketJob($booking)])->dispatch();

Bus::batch($jobs)
    ->then(fn ($batch) => /* ZIP banao */)
    ->dispatch();

class ExportBookingsJob implements ShouldQueue, ShouldBeUnique { /* ... */ }
```

Batches ke liye `job_batches` table chahiye (Laravel 11 ki default migration me aati hai).

### 19. Supervisor aur Horizon (Phase 5)

**Kya hai:** `queue:work` terminal band hote hi band ho jata hai. Supervisor (Linux tool) worker ko zinda rakhta hai aur crash par restart karta hai. Horizon Redis queues ka dashboard hai. **Kyun / fayda:** Production me workers hamesha chalu rehte hain. **Dhyaan rakho:** Shared hosting par Supervisor nahi milta, uske liye VPS chahiye. Shared me cron se `php artisan queue:work --stop-when-empty` chala sakte ho. Deploy ke baad `php artisan queue:restart` zaroor chalao, warna worker purana code chalata rehta hai.

### 20. Service Container (Phase 7)

**Kya hai:** Laravel ka object factory. Classes ko unki zaroorat ki cheezein (dependencies) khud deta hai. **Kyun / fayda:** Code loosely coupled rehta hai, aur test me nakli version daalna aasan hota hai. **Case:** PaymentGateway interface. Aaj Razorpay, kal Stripe, bas ek line badalni hai. **Example:** Restaurant me aap paani maangte ho, kaunsa brand dena hai, waiter decide karta hai.

```php
// AppServiceProvider::register
$this->app->bind(PaymentGateway::class, RazorpayGateway::class);

// Kahin bhi, Laravel khud inject karega
public function __construct(private PaymentGateway $gateway) {}
```

### 21. Service / Action Classes (Phase 3)

**Kya hai:** Business logic controller se nikal kar alag class me. **Kyun / fayda:** Controller patla rehta hai, logic web, API aur command teeno me reuse hota hai, aur test aasan. **Case:** Booking ka poora logic (lock, seat check, booking create) ek jagah.

```php
class BookTicketAction
{
    public function execute(User $user, TicketType $type, int $qty): Booking
    {
        return DB::transaction(function () use ($user, $type, $qty) {
            // lock, seat check, booking create
        });
    }
}

// Controller sirf 2-3 line
public function store(BookRequest $request, BookTicketAction $action)
{
    $booking = $action->execute($request->user(), TicketType::findOrFail($request->ticket_type_id), $request->qty);
    return redirect()->route('bookings.show', $booking);
}
```

### 22. Events, Listeners, Observers (Phase 7)

**Kya hai:** Event matlab 'kuch hua' ka signal. Listener us signal par kaam karta hai. Observer model ke create, update, delete par hook hai. **Kyun / fayda:** Booking ke code me email, SMS aur analytics ka code nahi ghusana padta. **Example:** Fire alarm baj gaya (event), to guard, lift aur doors apna apna kaam karte hain (listeners).

```php
BookingCreated::dispatch($booking);

class SendTicketEmail implements ShouldQueue
{
    public function handle(BookingCreated $event): void { /* mail bhejo */ }
}
```

Pitfall: Observer me heavy logic mat likho. Observer ke andar wahi model save karoge to infinite loop ban sakta hai, wahan `saveQuietly()` use karo.

### 23. Middleware (Phase 7)

**Kya hai:** Request controller tak pahunchne se pehle ka checkpoint. **Kyun / fayda:** Common checks (login, role, language, logging) ek jagah. **Example:** Airport security, boarding se pehle sabko guzarna padta hai.

```php
// php artisan make:middleware EnsureIsOrganizer
public function handle($request, Closure $next)
{
    abort_unless($request->user()?->organizer_id, 403);
    return $next($request);
}

Route::middleware(['auth', 'organizer'])->group(function () { /* ... */ });
```

Laravel 11 me alias `bootstrap/app.php` me `$middleware->alias([...])` se banta hai.

### 24. Testing: Pest, Factories (Phase 8)

**Kya hai:** Code ko code se check karna. **Kyun / fayda:** Refactor me darr nahi lagta aur bug jaldi pakde jate hain. **Case:** IDOR, overbooking, validation, policies ke tests.

```php
// RefreshDatabase import karo
uses(RefreshDatabase::class); // har test ke baad DB saaf

it('user dusre ki booking nahi dekh sakta', function () {
    $booking = Booking::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($other)
        ->get(route('bookings.show', $booking))
        ->assertNotFound();
});
```

Factory nakli data banati hai, jaise `Booking::factory()->count(50)->create()`.

### 25. Fakes (Phase 8)

**Kya hai:** Asli email, queue, storage ya API ki jagah nakli version, taaki bina kuch bheje check kar sako ki 'bheja gaya'. **Kyun / fayda:** Tests fast chalte hain, internet ya mail server nahi chahiye.

```php
Queue::fake();
$this->post('/book', $data);
Queue::assertPushed(SendTicketJob::class);

Mail::fake();
Storage::fake('public');
Http::fake(['api.razorpay.com/*' => Http::response(['status' => 'ok'])]);
```

### 26. Cache: Tags, Atomic Locks (Phase 6)

**Kya hai:** Mehengi query ka result yaad rakhna. **Kyun / fayda:** Dashboard har refresh par heavy query nahi chalata. **Example:** Teacher answer board par ek baar likh deti hai, har bachche ke sawal par dobara solve nahi karti.

```php
$stats = Cache::remember("organizer:{$id}:stats", 600, fn () => /* heavy query */);

// Naya booking aaye to purana hatao
Cache::forget("organizer:{$id}:stats");

// Atomic lock: same kaam do baar ek saath na chale
Cache::lock('export:' . $id, 30)->get(function () { /* ... */ });
```

Tags (`Cache::tags(['events'])->flush()`) sirf Redis ya Memcached me chalte hain, file ya database driver me nahi. Cache expire hote hi hazaaron requests ek saath heavy query chalayein, ise stampede kehte hain, atomic lock isse bachata hai.

### 27. Scheduler (Phase 8)

**Kya hai:** Cron jobs ko Laravel code me define karna. **Kyun / fayda:** Server par sirf ek cron entry lagti hai, baaki sab code me. **Case:** Event se 1 din pehle reminder, unpaid bookings cancel karna.

```php
// routes/console.php (Laravel 11)
Schedule::command('events:send-reminders')->dailyAt('09:00')->withoutOverlapping();
```

Server cron: `* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1`

`withoutOverlapping()` ka matlab pichla run chal raha ho to naya shuru nahi hoga. Laravel 10 me schedule `app/Console/Kernel.php` me hota hai.

### 28. Deploy aur Logging (Phase 8)

**Kya hai:** Production me app ko fast aur safe chalane ki commands aur habits.

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan queue:restart
```

`config:cache` ke baad `env()` sirf config files me kaam karta hai, code me seedha `env()` mat use karo. Production me `APP_DEBUG=false` rakho.

**Logging:** `Log::error('Payment fail', ['booking_id' => $id]);` likho taaki baad me pata chale kya hua. Sentry ya Flare jaise tool error aate hi alert dete hain. **Example:** Dukaan ka CCTV: kuch galat ho to record dekh sakte ho.
