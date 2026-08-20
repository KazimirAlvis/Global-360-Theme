# Stage 2B Development Checkpoint

Stage 2B is functionally validated as a development checkpoint. It is not
approved for merge or production deployment because representative database
query counts regressed during verification.

| Route | Before | Stage 2B |
| --- | ---: | ---: |
| Homepage | 95 | 102 |
| Clinic | 82 | 121 |
| Doctor | 81 | 120 |
| Find a Doctor | 173 | 177 |
| Populated state | 88 | 140 |

Stage 2B must not be described as a database-performance improvement. Stage 2C
must profile and address the Clinic, Doctor, and state-route regressions before
these branches are considered for merge or deployment.

The Stage 2B asset result remains valid independently: the full Global Blocks
CSS bundle is no longer loaded globally, and block/context styles are scoped to
the pages that use them.

## Stage 2C local remediation

The uncommitted Stage 2C remediation removed unconditional frontend theme-cache
purges, primed bulk Clinic/Doctor post-meta caches, and reused one request-scoped
relationship index. Three repeated local profiling passes produced these stable
results (the Homepage varied between 66 and 68 queries):

| Route | Stage 2B | Stage 2C |
| --- | ---: | ---: |
| Homepage | 102 | 66–68 |
| Clinic | 121 | 55 |
| Doctor | 120 | 55 |
| Find a Doctor | 177 | 79 |
| Populated state | 140 | 61 |

Duplicate queries fell from 16 to 1 on every measured route. The remaining
duplicate lookup for post ID 3 predates and sits outside the Stage 2B regression;
it is not a blocker for this checkpoint.

| Route | Stage 2B SQL time | Stage 2C median SQL time |
| --- | ---: | ---: |
| Homepage | 22.644 ms | ~2.9 ms |
| Clinic | 6.330 ms | ~3.2 ms after a cold-run outlier |
| Doctor | 5.526 ms | ~2.9 ms |
| Find a Doctor | 8.484 ms | ~3.5 ms |
| Populated state | 5.758 ms | ~3.5–4.2 ms |

The relationship index is request-local only. Its first consumer bulk-loads
Doctor and linked Clinic relationship data, producing a modest request-memory
increase while eliminating N+1 relationship reads. Monitor this tradeoff if the
dataset grows substantially. No persistent or transient cache benefit is
claimed.

Frontend map verification uses stored coordinates only; no synchronous
geocoding was reintroduced. Platform Core keeps canonical `latitude` and
`longitude` fields, while the Theme compatibility resolver accepts those names
and the legacy `lat` and `lng` aliases. The representative Clinic map is visible
with its canonical coordinates, and populated state markers are restored.

These results make the query regression technically resolved in local testing.
Stage 2C still requires review and commit before the combined work is eligible
for merge or staging rollout.
