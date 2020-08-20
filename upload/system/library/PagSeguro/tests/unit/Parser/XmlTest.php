<?php

use PHPUnit\Framework\TestCase;
use ValdeirPsr\PagSeguro\Parser\Xml;

class XmlTest extends TestCase
{
    /**
     * @test
     */
    public function newInstance()
    {
        $instance = new Xml();
        $this->assertInstanceOf(Xml::class, $instance);
    }

    /**
     * @test
     */
    public function parserArrayWithAnIndex()
    {
        $arr = [
            'name' => 'Valdeir Psr'
        ];

        $instance = new Xml();
        $result = $instance->parser($arr);

        $this->assertXmlStringEqualsXmlFile(
            'tests/data/parser/xml/parserArrayWithAnIndex.xml',
            $result->saveXML()
        );
    }

    /**
     * @test
     */
    public function parserArrayWIthMultipleIndexes()
    {
        $arr = [
            'name' => 'Valdeir Psr',
            'site' => 'https://valdeir.dev'
        ];

        $instance = new Xml();
        $result = $instance->parser($arr);

        $this->assertXmlStringEqualsXmlFile(
            'tests/data/parser/xml/parserArrayWIthMultipleIndexes.xml',
            $result->saveXML()
        );
    }

    /**
     * @test
     */
    public function parserMatriz()
    {
        $arr = [
            'cdz' => [
                'personagens' => [
                    [
                        'nome_original' => '天馬星座の星矢',
                        'name' => 'Seiya',
                        'outros_nomes' => [
                            'Seiya de Pégaso',
                            'Seiya de Pégasus'
                        ],
                        'constelacao' => 'Pegasus',
                        'armadura' => 'Pégaso',
                        'divindade' => 'Atena',
                        'classificacao' => 'Cavaleiro de Bronze'
                    ],

                    [
                        'nome_original' => '龍星座の紫龍',
                        'name' => 'Shiryu',
                        'outros_nomes' => [
                            'Shiryu de Dragão'
                        ],
                        'constelacao' => 'Draco',
                        'armadura' => 'Dragão',
                        'divindade' => 'Atena',
                        'classificacao' => 'Cavaleiros de Bronze'
                    ],

                    [
                        'nome_original' => 'アンドロメダ星座の瞬',
                        'name' => 'Shun',
                        'outros_nomes' => [
                            'Shun de Andrômeda'
                        ],
                        'constelacao' => 'Andrômeda',
                        'armadura' => 'Andrômeda',
                        'divindade' => 'Atena',
                        'classificacao' => 'Cavaleiro de Bronze'
                    ],

                    [
                        'nome_original' => '鳳凰星座の一輝',
                        'name' => 'Ikki',
                        'outros_nomes' => [
                            'Ikki Kido',
                        ],
                        'constelacao' => 'Fênix',
                        'armadura' => 'Fênix',
                        'divindade' => 'Atena',
                        'classificacao' => 'Cavaleiro de Bronze'
                    ],

                    [
                        'nome_original' => 'Hyoga',
                        'name' => '白鳥星座の氷河',
                        'constelacao' => 'Cygnus',
                        'armadura' => 'Cisne',
                        'divindade' => [
                            'Atena',
                            'Odin'
                        ],
                        'classificacao' => 'Cavaleiro de Bronze'
                    ]
                ]
            ]
        ];

        $instance = new Xml();
        $result = $instance->parser($arr);

        $this->assertXmlStringEqualsXmlFile(
            'tests/data/parser/xml/parserMatriz.xml',
            $result->saveXML()
        );
    }
}
