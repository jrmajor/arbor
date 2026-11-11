title = Relationship
check_with = Check relationship with
not_found = No connection between them was found in the tree.
common_ancestors = { $count ->
    [one] Common ancestor
   *[other] Common ancestors
}
by_marriage = Related by marriage, with no common ancestor in the tree.
and = and
common_ancestor = common ancestor

step =
    .parent = { $sex ->
        [xx] mother
        [xy] father
       *[other] parent
    }
    .child = { $sex ->
        [xx] daughter
        [xy] son
       *[other] child
    }
    .spouse = { $sex ->
        [xx] wife
        [xy] husband
       *[other] spouse
    }

kind =
    .self = the same person
    .parent = { $sex ->
        [xx] mother
        [xy] father
       *[other] parent
    }
    .child = { $sex ->
        [xx] daughter
        [xy] son
       *[other] child
    }
    .grandparent = { $great }{ $sex ->
        [xx] grandmother
        [xy] grandfather
       *[other] grandparent
    }
    .grandchild = { $great }{ $sex ->
        [xx] granddaughter
        [xy] grandson
       *[other] grandchild
    }
    .sibling = { $half ->
        [true] half-
       *[other] {""}
    }{ $sex ->
        [xx] sister
        [xy] brother
       *[other] sibling
    }
    .uncle_aunt = { $great }{ $sex ->
        [xx] aunt
        [xy] uncle
       *[other] aunt or uncle
    }
    .nephew_niece = { $great }{ $sex ->
        [xx] niece
        [xy] nephew
       *[other] niece or nephew
    }
    .cousin = { $degree ->
        [1] first
        [2] second
        [3] third
        [4] fourth
        [5] fifth
       *[other] { $degree }th
    } cousin{ $removed ->
        [0] {""}
        [1] {" "}once removed
        [2] {" "}twice removed
       *[other] {" "}{ $removed } times removed
    }
    .spouse = { $sex ->
        [xx] wife
        [xy] husband
       *[other] spouse
    }
    .parent_in_law = { $sex ->
        [xx] mother-in-law
        [xy] father-in-law
       *[other] parent-in-law
    }
    .child_in_law = { $sex ->
        [xx] daughter-in-law
        [xy] son-in-law
       *[other] child-in-law
    }
    .sibling_in_law = { $sex ->
        [xx] sister-in-law
        [xy] brother-in-law
       *[other] sibling-in-law
    }
    .co_sibling_in_law = { $sex ->
        [xx] sister-in-law
        [xy] brother-in-law
       *[other] sibling-in-law
    }
    .step_parent = { $sex ->
        [xx] stepmother
        [xy] stepfather
       *[other] step-parent
    }
    .step_child = { $sex ->
        [xx] stepdaughter
        [xy] stepson
       *[other] stepchild
    }
    .in_law = relative by marriage
