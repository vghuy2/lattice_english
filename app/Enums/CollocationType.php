<?php

namespace App\Enums;

enum CollocationType: string
{
    case VERB_NOUN = 'verb_noun';
    case ADJ_NOUN = 'adj_noun';
    case VERB_PREP = 'verb_prep';
    case NOUN_NOUN = 'noun_noun';
    case ADV_ADJ = 'adv_adj';
    case PHRASE = 'phrase';

    public function label(): string
    {
        return match ($this) {
            self::VERB_NOUN => 'Động từ + Danh từ (Verb + Noun)',
            self::ADJ_NOUN => 'Tính từ + Danh từ (Adj + Noun)',
            self::VERB_PREP => 'Động từ + Giới từ (Verb + Prep)',
            self::NOUN_NOUN => 'Danh từ + Danh từ (Noun + Noun)',
            self::ADV_ADJ => 'Trạng từ + Tính từ (Adv + Adj)',
            self::PHRASE => 'Cụm diễn đạt học thuật (Phrase)',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::VERB_NOUN => 'V + N',
            self::ADJ_NOUN => 'Adj + N',
            self::VERB_PREP => 'V + Prep',
            self::NOUN_NOUN => 'N + N',
            self::ADV_ADJ => 'Adv + Adj',
            self::PHRASE => 'Phrase',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::VERB_NOUN => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            self::ADJ_NOUN => 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20',
            self::VERB_PREP => 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20',
            self::NOUN_NOUN => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            self::ADV_ADJ => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
            self::PHRASE => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
        };
    }
}
