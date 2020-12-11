<?php

namespace heinthanth\Uit\Lexer;

require_once __DIR__ . '/TokenDefinition.php';

define('DIGIT_STRING', '0123456789');
define('LETTER_STRING', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
define('LETTER_W_DIGIT_STRING', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

class Lexer
{
    /**
     * Position of Lexer cursor
     * @var int
     */
    private int $pos = -1;

    /**
     * Current character at Lexer cursor position
     * @var string
     */
    private string $currentCharacter = "\0";

    /**
     * Lexer constructor.
     * @param string $code Source Code to tokenize
     */
    public function __construct(private string $code)
    {
        $this->goNext();
    }

    /**
     * Move to next character by incrementing Cursor Position.
     */
    private function goNext(): void
    {
        $this->pos++;
        $this->currentCharacter = $this->pos < strlen($this->code) ? $this->code[$this->pos] : "\0";
    }

    /**
     * Tokenize source code
     * @return array
     */
    public function tokenize(): array
    {
        $tokens = [];
        while ($this->currentCharacter !== "\0") {
            if ($this->currentCharacter === ' ' || $this->currentCharacter === "\t") {
                $this->goNext();
            } elseif (str_contains(DIGIT_STRING, $this->currentCharacter)) {
                $tokens[] = $this->makeNumber();
            } elseif (str_contains(LETTER_STRING, $this->currentCharacter)) {
                $tokens[] = $this->makeIdentifier();
            } elseif ($this->currentCharacter === '+') {
                $tokens[] = new Token(T_PLUS);
                $this->goNext();
            } elseif ($this->currentCharacter === '-') {
                $tokens[] = new Token(T_MINUS);
                $this->goNext();
            } elseif ($this->currentCharacter === '*') {
                $tokens[] = new Token(T_STAR);
                $this->goNext();
            } elseif ($this->currentCharacter === '/') {
                $tokens[] = new Token(T_SLASH);
                $this->goNext();
            } elseif ($this->currentCharacter === '%') {
                $tokens[] = new Token(T_PERCENT);
                $this->goNext();
            } elseif ($this->currentCharacter === '^') {
                $tokens[] = new Token(T_CARET);
                $this->goNext();
            } elseif ($this->currentCharacter === '=') {
                $tokens[] = new Token(T_EQUAL);
                $this->goNext();
            } elseif ($this->currentCharacter === '(') {
                $tokens[] = new Token(T_LPARAN);
                $this->goNext();
            } elseif ($this->currentCharacter === ')') {
                $tokens[] = new Token(T_RPARAN);
                $this->goNext();
            } else {
                // invalid token
                die("Error: Invalid Syntax" . PHP_EOL);
            }
        }
        $tokens[] = new Token(T_EOF);
        return $tokens;
    }

    /**
     * Analyze Number strings
     * @return Token
     */
    private function makeNumber(): Token
    {
        $numberString = '';
        $dotCount = 0;
        while ($this->currentCharacter !== null && str_contains(DIGIT_STRING . '.', $this->currentCharacter)) {
            if ($this->currentCharacter === '.') {
                if ($dotCount === 1) break;
                $dotCount++;
                $numberString .= '.';
            } else {
                $numberString .= $this->currentCharacter;
            }
            $this->goNext();
        }
        return new Token(T_NUMBER, $numberString);
    }

    private function makeIdentifier(): Token
    {
        $identifierString = '';
        while ($this->currentCharacter !== null && str_contains(LETTER_W_DIGIT_STRING . '_', $this->currentCharacter)) {
            $identifierString .= $this->currentCharacter;
            $this->goNext();
        }
        return new Token(in_array($identifierString, UIT_KEYWORD) ? T_KEYWORD : T_IDENTIFIER, $identifierString);
    }
}
