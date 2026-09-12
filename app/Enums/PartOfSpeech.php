<?php

namespace App\Enums;

enum PartOfSpeech: string
{
    case NOUN = 'noun';
    case VERB = 'verb';
    case ADJECTIVE = 'adjective';
    case ADVERB = 'adverb';
    case PHRASE = 'phrase';
    case COLLOCATION = 'collocation';
    case IDIOM = 'idiom';

    public function label(): string
    {
        return match ($this) {
            self::NOUN => 'Danh từ (n)',
            self::VERB => 'Động từ (v)',
            self::ADJECTIVE => 'Tính từ (adj)',
            self::ADVERB => 'Trạng từ (adv)',
            self::PHRASE => 'Cụm từ (phrase)',
            self::COLLOCATION => 'Collocation',
            self::IDIOM => 'Thành ngữ (idiom)',
        };
    }

    public function short(): string
    {
        return match ($this) {
            self::NOUN => 'n',
            self::VERB => 'v',
            self::ADJECTIVE => 'adj',
            self::ADVERB => 'adv',
            self::PHRASE => 'phrase',
            self::COLLOCATION => 'colloc',
            self::IDIOM => 'idiom',
        };
    }
}
