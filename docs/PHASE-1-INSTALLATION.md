# BBHS Phase 1 – Organisational Architecture

This patch adds the database foundation for graduation sets, geographical branches, executive positions and assignments, optional branch affiliation, independent membership verification, audit history, and scoped events.

## Included

- 57 graduation sets (1970–2026)
- Initial branches: Lagos, Abeokuta, Abuja and Ibadan
- Branch-to-city mapping (extendable by administrators)
- Configurable global, set and branch executive positions
- Executive appointment/tenure records
- Optional member-to-branch affiliation records
- Separate set/branch verification records
- Membership audit log
- Event ownership fields and multi-scope event targeting table
- Legacy `class_set` mapping where the year is unambiguous

## Installation

1. Back up the database and codebase.
2. Copy the included files into the matching paths in the Laravel project.
3. Merge the supplied `DatabaseSeeder.php` replacement, or add `BbhsOrganisationSeeder::class` to the existing seeder.
4. Run migrations in staging first: `php artisan migrate`.
5. Seed the organisational defaults: `php artisan db:seed --class=BbhsOrganisationSeeder`.
6. Verify the 57 sets and four initial branches in the database.

## Important integration notes

- `alumni_profiles.graduation_set_id` is nullable during migration to preserve existing records. The registration workflow must require a valid set before a new member can submit registration. Existing unmapped legacy values must be reviewed by an administrator before being made visible.
- `alumni_profiles.class_set` is retained for backward compatibility. Do not remove it until all old code references have been migrated and verified.
- `member_branches` is optional. A member does not need a branch affiliation to be included in local branch event visibility.
- Branch event visibility must match the member's normalised city/country against `branch_cities`, or their approved `member_branches` affiliation. City matching must not silently create a branch membership.
- A set approval is the gate for member directory visibility. Branch verification is a separate, non-blocking process.
- `leadership_assignments.scope_id` refers to a graduation set or branch according to `scope_type`; global assignments use `NULL`. Validate this polymorphic scope in application services because a conventional foreign key cannot target two different tables.
- The migration is additive. Test it against a copy of the current MySQL database before production. Do not run `migrate:fresh` on the live system.
- The supplied source archive does not include `vendor/` or a live database connection, so application-level migration execution and automated tests must be run in the actual project environment.
