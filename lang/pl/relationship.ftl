title = Pokrewieństwo
check_with = Sprawdź pokrewieństwo z
not_found = Nie znaleziono w drzewie żadnego powiązania między nimi.
common_ancestors = { $count ->
    [one] Wspólny przodek
   *[other] Wspólni przodkowie
}
by_marriage = Powinowactwo przez małżeństwo, bez wspólnego przodka w drzewie.
and = i
common_ancestor = wspólny przodek

step =
    .parent = { $sex ->
        [xx] matka
        [xy] ojciec
       *[other] rodzic
    }
    .child = { $sex ->
        [xx] córka
        [xy] syn
       *[other] dziecko
    }
    .spouse = { $sex ->
        [xx] żona
        [xy] mąż
       *[other] małżonek
    }

kind =
    .self = ta sama osoba
    .parent = { $sex ->
        [xx] matka
        [xy] ojciec
       *[other] rodzic
    }
    .child = { $sex ->
        [xx] córka
        [xy] syn
       *[other] dziecko
    }
    .grandparent = { $pra }{ $sex ->
        [xx] babcia
       *[other] dziadek
    }
    .grandchild = { $pra }{ $sex ->
        [xx] wnuczka
       *[other] wnuk
    }
    .sibling = { $sex ->
        [xx] siostra{ $half ->
            [true] {" "}przyrodnia
           *[other] {""}
        }
       *[other] brat{ $half ->
            [true] {" "}przyrodni
           *[other] {""}
        }
    }
    .uncle_aunt = { $greats ->
        [0] { $sex ->
            [xx] ciotka
           *[other] { $side ->
                [maternal] wuj
               *[other] stryj
            }
        }
       *[other] { $sex ->
            [xx] siostra
           *[other] brat
        } { $pra }{ $via ->
            [xx] babci
           *[other] dziadka
        }
    }
    .nephew_niece = { $greats ->
        [0] { $via ->
            [xx] { $sex ->
                [xx] siostrzenica
               *[other] siostrzeniec
            }
           *[other] { $sex ->
                [xx] bratanica
               *[other] bratanek
            }
        }
       *[other] { $pra }{ $sex ->
            [xx] wnuczka
           *[other] wnuk
        } { $via ->
            [xx] siostry
           *[other] brata
        }
    }
    .cousin = { $removed ->
        [0] { $degree ->
            [1] { $sex ->
                [xx] siostra { $line ->
                    [stryjeczny] stryjeczna
                    [wujeczny] wujeczna
                   *[other] cioteczna
                }
               *[other] brat { $line ->
                    [stryjeczny] stryjeczny
                    [wujeczny] wujeczny
                   *[other] cioteczny
                }
            }
           *[other] { $sex ->
                [xx] kuzynka
               *[other] kuzyn
            } { $degree ->
                [1] pierwszego
                [2] drugiego
                [3] trzeciego
                [4] czwartego
                [5] piątego
                [6] szóstego
               *[other] { $degree }.
            } stopnia
        }
       *[other] { $sex ->
            [xx] kuzynka
           *[other] kuzyn
        } { $degree ->
            [1] pierwszego
            [2] drugiego
            [3] trzeciego
            [4] czwartego
            [5] piątego
            [6] szóstego
           *[other] { $degree }.
        } stopnia, { $older ->
            [true] { $sex ->
                [xx] starsza
               *[other] starszy
            }
           *[other] { $sex ->
                [xx] młodsza
               *[other] młodszy
            }
        } o { $removed } { $removed ->
            [one] pokolenie
            [few] pokolenia
           *[other] pokoleń
        }
    }
    .spouse = { $sex ->
        [xx] żona
        [xy] mąż
       *[other] małżonek
    }
    .parent_in_law = { $sex ->
        [xx] teściowa
       *[other] teść
    }
    .child_in_law = { $sex ->
        [xx] synowa
       *[other] zięć
    }
    .sibling_in_law = { $sex ->
        [xx] { $kind ->
            [sibling_spouse] { $via ->
                [xy] bratowa
               *[other] szwagierka
            }
           *[other] szwagierka
        }
       *[other] szwagier
    }
    .step_parent = { $sex ->
        [xx] macocha
       *[other] ojczym
    }
    .step_child = { $sex ->
        [xx] pasierbica
       *[other] pasierb
    }
    .in_law = { $sex ->
        [xx] spowinowacona
       *[other] spowinowacony
    }
