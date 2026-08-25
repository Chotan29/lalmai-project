# Fee heads with groups — final analysis

One head the student sees, many heads the accounts see.

*Supersedes `admission-fee-split-architecture.md`.*

---

## 1. The problem

A student pays one amount. The college needs that amount broken across many heads —
26 of them for স্নাতক (সম্মান) ১ম বর্ষ admission alone, and the same shape repeats for exam,
transport and hostel fees.

Today `RegistrationPaymentController` writes **one** `fee_masters` row and **one**
`fee_collections` row under a head called `ADMISSION FEE`. So 7,400 taka sits in a single
bucket: no head-wise figure, no treasury share, no view of what belongs to the departments.

## 2. What the code already does — the finding that shrinks this job

**The distribution engine already exists and is in production.**

`FeesCollectionController` "Quick Receive" (lines 432–476) loads a student's `fee_masters`
ordered by due date, then walks them paying each in full until the money runs out, with the
remainder landing on the last one.

So splitting one payment across many heads is not new work. The only thing missing is that
**admission creates one `fee_masters` row instead of 26.** Give it 26 and the existing code
distributes correctly.

Two more things already built:

- `FeeCollectionHeadReportController` — head-wise collection reports, daily / weekly / monthly /
  yearly. It reports nothing useful today only because every taka is in one head.
- `AccountingScope::activeFeeHead()` — a **single method** feeding every head dropdown in the
  system, called from 10 places. One chokepoint to extend, not ten screens to hunt through.

## 3. Correction to an earlier claim

I previously described the instalment figures as coming from "an API", implying an external
system. That was wrong. `currentUnpaidInstallment()` calls
`API\PaymentController@getStudentInfo` **in-process** via `app()->call()`; it ends up in
`FeeMaster::calculateInstallments()`.

On inspection, that method now returns a **single instalment at 100%** of the outstanding
amount. The `INSTALLMENT_PERCENTAGES = [30, 40, 30]` constant still sits in the file but no live
path uses it. The only place still genuinely producing a 30/40/30 split was the fallback in
`UserStudent\HomeController`, now switched off and left commented with its reasoning.

The code and the office's "no part payment" rule now agree.

## 4. The model

A permanent library of sub heads, and groups that assemble them.

```
fee_heads              unchanged — the permanent, reusable list of heads
fee_head_groups        the main head a student sees: name, session, amount, status, locked
fee_head_group_items   group_id, fee_head_id, amount, sort_order
```

### Why a shared library rather than parent_id

With `parent_id`, a sub head belongs to exactly one parent. "লাইব্রেরি" under ভর্তি ফি could not
also sit under পরীক্ষা ফি — you would create a second one. Within a few years the head list
carries লাইব্রেরি, লাইব্রেরি (পরীক্ষা), লাইব্রেরি ২০২৬, and *"how much came in under লাইব্রেরি this
year"* needs someone to remember all three.

With a shared library there is **one লাইব্রেরি, for ever.** It is attached wherever needed, at
whatever amount that fee calls for. A head-wise figure is then a single group-by across every
fee that uses it.

It also dissolves a schema problem for free: `fee_head_title` is globally `unique`, so two heads
named "বিবিধ" cannot both exist. With one shared "বিবিধ" attached to several groups, the
constraint never gets in the way and does not need changing.

### Why amounts live on the join, not the head

The same head is worth 25 in admission and 10 in an exam fee. Putting the amount on
`fee_head_group_items` lets that happen, and it makes year-on-year versioning almost free: a new
session is a new group plus 26 join rows. The head list itself never grows, and last year's
amounts stay in last year's group.

## 5. Rules

1. **Sub-head amounts must total the group amount, enforced on save.** A group of 7,400 whose
   items add to 7,300 is a bug waiting for admission day. The screen shows a running total and
   refuses to save while they disagree.

   This also removes a question that would otherwise reach the payment code: does each head take
   its exact figure, or a proportional share? If the two always agree, both answers are the same
   answer, and no rounding dust is ever created.

2. **Money is recorded against sub heads, never the group.** The group is a label for the
   student, not a balance. Were money allowed to sit on both, some report would eventually count
   it twice. The group figure is never lost — it is the roll-up.

3. **No part payment.** The student pays the group amount in full, per the office's rule.

4. **One bank reference across all 26 rows.** One transaction expands to 26 heads; any head
   traces back to the bank.

5. **A group that has taken money is locked.** Editing it would rewrite what students already
   paid. Next session is a duplicate.

## 6. What has to change

| Where | Change | Size |
|---|---|---|
| migration | `fee_head_groups`, `fee_head_group_items` | small |
| `AccountingScope` | add `activeFeeHeadGroup()` beside `activeFeeHead()` | small |
| new screen | group builder — attach heads, amounts, live total, save blocked on mismatch | medium |
| admission program | one dropdown: which group applies | small |
| `RegistrationPaymentController` | write N rows instead of 1 | small |
| reports | treasury / department filters | small |

**Existing accounting screens and reports need no change at all.** Collections still point at
ordinary `fee_heads` rows exactly as they do today; the group layer is additive. That is the
main reason to prefer this shape — it cannot break what is already working.

### Two flags worth adding to `fee_heads`

`is_treasury` (বিদ্যুৎ 10, ভর্তি 25, টিউশন 300) and `collected_by` = college / department (the
2,800). Both turn a memorised list into a one-query report.

### One duplication to clean up while here

The excluded-head list `[61, 74, 75]` is written twice — `config/api.php` and
`FeeMaster::$excludedFeeHeads`. Two copies of the same truth drift apart eventually. It belongs
on the head as a flag.

## 7. Decided

- Sub heads are created once and reused; groups are created when needed and heads attached.
- The group carries the amount the student pays.
- No part payment.
- The invented 30/40/30 instalment split is off.

## 8. Still open

**Does the gateway take 7,400 or 4,600?** The fee list says the 2,800 is collected by the
department. Charging it online means the college holds departmental money until it is handed
over — legitimate, but it should be a decision rather than an accident. **This determines how
many heads go in the group, so it blocks the build.**

**Waivers.** If a student is charged less than the group amount, which head gives way? Not
urgent — there are no waivers today — but by rule 1 a discounted admission will be refused
rather than guessed at, so the answer is needed before the first one.

## 9. Order of work

migration and models → group builder screen → attach group to admission program →
`RegistrationPaymentController` writes N rows → **prove it locally with a real payment, checking
all 26 heads reconcile to the paid amount** → deploy.

## 10. What happens to existing data

Existing single-row `ADMISSION FEE` collections stay exactly as they are. They are history, and
history should not be rewritten by a deploy. New admissions split; old ones remain one line and
report under that head.
