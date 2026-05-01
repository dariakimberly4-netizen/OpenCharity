<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_members', function (Blueprint $table): void {
            $table->string('code')->nullable()->unique()->after('id');
        });

        DB::transaction(function (): void {
            $families = DB::table('families')->select('id', 'code')->get();

            foreach ($families as $family) {
                $familySeq = explode('-', $family->code)[1];

                $members = DB::table('family_members')
                    ->where('family_id', $family->id)
                    ->orderBy('id')
                    ->select('id')
                    ->get();

                foreach ($members as $index => $member) {
                    $memberCode = sprintf('M-%s-%04d', $familySeq, $index + 1);
                    DB::table('family_members')->where('id', $member->id)->update(['code' => $memberCode]);
                }
            }

            $allMembers = DB::table('family_members')
                ->select('id', 'code', 'family_id')
                ->whereNotNull('code')
                ->get()
                ->keyBy('id');

            $families2 = DB::table('families')->select('id', 'code')->get()->keyBy('id');

            $casesByMember = DB::table('charity_cases')
                ->select('id', 'family_id', 'family_member_id')
                ->orderBy('id')
                ->get()
                ->groupBy('family_member_id');

            foreach ($casesByMember as $memberId => $cases) {
                $member = $allMembers[$memberId] ?? null;

                if (! $member) {
                    continue;
                }

                $parts = explode('-', $member->code);
                $familySeq = $parts[1];
                $memberSeq = $parts[2];

                foreach ($cases as $index => $case) {
                    $caseCode = sprintf('C-%s-%s-%04d', $familySeq, $memberSeq, $index + 1);
                    DB::table('charity_cases')->where('id', $case->id)->update(['code' => $caseCode]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('family_members', function (Blueprint $table): void {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};
