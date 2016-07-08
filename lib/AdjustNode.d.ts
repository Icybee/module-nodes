declare namespace Icybee.Nodes {

    namespace AdjustNode {

        interface ChangeEvent extends Icybee.Adjust.ChangeEvent {
            selected: Element
        }

    }

    class AdjustNode extends Icybee.Adjust {

    }

}
