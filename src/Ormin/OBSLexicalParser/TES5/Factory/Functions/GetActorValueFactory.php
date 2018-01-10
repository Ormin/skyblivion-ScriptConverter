<?php

namespace Ormin\OBSLexicalParser\TES5\Factory\Functions;

use Ormin\OBSLexicalParser\TES4\AST\Value\FunctionCall\TES4Function;
use Ormin\OBSLexicalParser\TES4\Context\ESMAnalyzer;
use Ormin\OBSLexicalParser\TES5\AST\Code\TES5CodeScope;
use Ormin\OBSLexicalParser\TES5\AST\Object\TES5ObjectCallArguments;
use Ormin\OBSLexicalParser\TES5\AST\Object\TES5Referencer;
use Ormin\OBSLexicalParser\TES5\AST\Scope\TES5GlobalScope;
use Ormin\OBSLexicalParser\TES5\AST\Scope\TES5MultipleScriptsScope;
use Ormin\OBSLexicalParser\TES5\AST\Value\Primitive\TES5String;
use Ormin\OBSLexicalParser\TES5\Exception\ConversionException;
use Ormin\OBSLexicalParser\TES5\Factory\TES5ExpressionFactory;
use Ormin\OBSLexicalParser\TES5\Factory\TES5ObjectCallArgumentsFactory;
use Ormin\OBSLexicalParser\TES5\Factory\TES5ObjectCallFactory;
use Ormin\OBSLexicalParser\TES5\Factory\TES5ObjectPropertyFactory;
use Ormin\OBSLexicalParser\TES5\Factory\TES5PrimitiveValueFactory;
use Ormin\OBSLexicalParser\TES5\Factory\TES5ReferenceFactory;
use Ormin\OBSLexicalParser\TES5\Factory\TES5ValueFactory;
use Ormin\OBSLexicalParser\TES5\Factory\TES5VariableAssignationFactory;
use Ormin\OBSLexicalParser\TES5\Service\MetadataLogService;
use Ormin\OBSLexicalParser\TES5\Service\TES5TypeInferencer;

class GetActorValueFactory implements FunctionFactory
{

    /**
     * @var string
     */


    /**
     * @var TES5ReferenceFactory
     */
    private $referenceFactory;

    /**
     * @var TES5ExpressionFactory
     */
    private $expressionFactory;

    /**
     * @var TES5VariableAssignationFactory
     */
    private $assignationFactory;

    /**
     * @var ESMAnalyzer
     */
    private $analyzer;

    /**
     * @var TES5ObjectPropertyFactory
     */
    private $objectPropertyFactory;

    /**
     * @var TES5PrimitiveValueFactory
     */
    private $primitiveValueFactory;

    /**
     * @var \Ormin\OBSLexicalParser\TES5\Service\TES5TypeInferencer
     */
    private $typeInferencer;

    /**
     * @var \Ormin\OBSLexicalParser\TES5\Service\MetadataLogService
     */
    private $metadataLogService;

    /**
     * @var TES5ValueFactory
     */
    private $valueFactory;

    /**
     * @var TES5ObjectCallFactory
     */
    private $objectCallFactory;

    /**
     * @var TES5ObjectCallArgumentsFactory
     */
    private $objectCallArgumentsFactory;

    public function __construct(TES5ValueFactory $valueFactory, TES5ObjectCallFactory $objectCallFactory, TES5ObjectCallArgumentsFactory $objectCallArgumentsFactory, TES5ReferenceFactory $referenceFactory, TES5ExpressionFactory $expressionFactory, TES5VariableAssignationFactory $assignationFactory, TES5ObjectPropertyFactory $objectPropertyFactory, ESMAnalyzer $analyzer, TES5PrimitiveValueFactory $primitiveValueFactory, TES5TypeInferencer $typeInferencer, MetadataLogService $metadataLogService)
    {

        $this->objectCallArgumentsFactory = $objectCallArgumentsFactory;
        $this->valueFactory = $valueFactory;
        $this->referenceFactory = $referenceFactory;
        $this->expressionFactory = $expressionFactory;
        $this->analyzer = $analyzer;
        $this->assignationFactory = $assignationFactory;
        $this->objectPropertyFactory = $objectPropertyFactory;
        $this->primitiveValueFactory = $primitiveValueFactory;
        $this->typeInferencer = $typeInferencer;
        $this->metadataLogService = $metadataLogService;
        $this->objectCallFactory = $objectCallFactory;
    }


    public function convertFunction(TES5Referencer $calledOn, TES4Function $function, TES5CodeScope $codeScope, TES5GlobalScope $globalScope, TES5MultipleScriptsScope $multipleScriptsScope)
    {
        $localScope = $codeScope->getLocalScope();
        $functionName = $function->getFunctionCall()->getFunctionName();
        $functionArguments = $function->getArguments();
        //@TODO - This should be fixed on expression-parsing level, with agression and confidence checks adjusted accordingly. There are no retail uses, so im not doing this for now ;)

        $actorValueMap = [
            'fatigue' => 'Stamina',
            'armorer' => 'Smithing',
            'security' => 'Lockpicking',
            'acrobatics' => 'Sneak',
            'mercantile' => 'Speechcraft',
            'mysticism' => 'Illusion',
            'blade' => 'OneHanded',
            'blunt' => 'OneHanded',
            'encumbrance' => 'InventoryWeight',
            'spellabsorbchance' => 'AbsorbChance',
            'resistfire' => 'FireResist',
            'resistfrost' => 'FrostResist',
            'resistdisease' => 'DiseaseResist',
            'resistmagic' => 'MagicResist',
            'resistpoison' => 'PoisonResist',
            'resistshock' => 'ElectricResist'
        ];
        
        $firstArg = $functionArguments->getValue(0);
        $convertedArguments = new TES5ObjectCallArguments();
        switch (strtolower($firstArg->getData())) {

            case 'strength':
            case 'intelligence':
            case 'willpower':
            case 'agility':
            case 'speed':
            case 'endurance':
            case 'personality':
            case 'luck': {

                if ($calledOn->getName() != "player") {
                    //We can't convert those.. and shouldn't be any, too.
                    throw new ConversionException("[ModAV] Cannot get attributes on non-player");
                }

                /**
                 *  Switch out callee with the reference to attr
                 */
                return $this->referenceFactory->createReadReference('TES4Attr' . ucwords(strtolower($firstArg->getData())),
                    $globalScope,
                    $multipleScriptsScope,
                    $localScope);

            }

            case 'fatigue':
            case 'armorer':
            case 'security':
            case 'acrobatics':
            case 'mercantile':
            case 'mysticism': //It doesn't exist in Skyrim - defaulting to Illusion..
            case 'blade':
            case 'blunt':
            case 'encumbrance':
            case 'spellabsorbchance':
            case 'resistfire':
            case 'resistfrost':
            case 'resistdisease':
            case 'resistmagic':
            case 'resistpoison':
            case 'resistshock': {
                $convertedArguments->add(new TES5String($actorValueMap[strtolower($firstArg->getData())]));
                return $this->objectCallFactory->createObjectCall($calledOn, $functionName, $multipleScriptsScope, $convertedArguments);
            }

            default: {
                $convertedArguments->add(new TES5String($firstArg->getData()));
                return $this->objectCallFactory->createObjectCall($calledOn, $functionName, $multipleScriptsScope, $convertedArguments);
            }

        }


    }
}