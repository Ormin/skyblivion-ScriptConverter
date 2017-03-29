<?php

namespace Ormin\OBSLexicalParser\TES5\Factory\Functions;

use Ormin\OBSLexicalParser\TES4\AST\Value\FunctionCall\TES4Function;
use Ormin\OBSLexicalParser\TES4\Context\ESMAnalyzer;
use Ormin\OBSLexicalParser\TES5\AST\Code\TES5CodeScope;
use Ormin\OBSLexicalParser\TES5\AST\Object\TES5ObjectCallArguments;
use Ormin\OBSLexicalParser\TES5\AST\Object\TES5Referencer;
use Ormin\OBSLexicalParser\TES5\AST\Scope\TES5GlobalScope;
use Ormin\OBSLexicalParser\TES5\AST\Scope\TES5MultipleScriptsScope;
use Ormin\OBSLexicalParser\TES5\AST\Value\Primitive\TES5Float;
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

class SetActorValueFactory implements FunctionFactory
{
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
        $functionArguments = $function->getArguments();
        $convertedArguments = new TES5ObjectCallArguments();

        $firstArg = $functionArguments->getValue(0);

        switch (strtolower($firstArg->getData())) {

            case 'strength':
            case 'intelligence':
            case 'willpower':
            case 'agility':
            case 'endurance':
            case 'personality':
            case 'luck': {

                if ($calledOn->getName() != "player") {
                    //We can't convert those.. and shouldn't be any, too.
                    throw new ConversionException("[SetAV] Cannot set attributes on non-player");
                }

                $functionName = "SetValue";
                $calledOn = $this->referenceFactory->createReference('TES4Attr' . ucwords(strtolower($firstArg->getData())),
                    $globalScope,
                    $multipleScriptsScope,
                    $localScope);

                $secondArg = $functionArguments->getValue(1);

                $convertedArguments->add($this->valueFactory->createValue($secondArg, $codeScope, $globalScope, $multipleScriptsScope));
                return $this->objectCallFactory->createObjectCall($calledOn, $functionName, $multipleScriptsScope, $convertedArguments);
                break;
            }

            case 'speed': {
                $functionName = "ForceMovementSpeed";
                $secondArg = $functionArguments->getValue(1);
                $convertedArguments->add($this->valueFactory->createValue($secondArg, $codeScope, $globalScope, $multipleScriptsScope));
                return $this->objectCallFactory->createObjectCall($calledOn, $functionName, $multipleScriptsScope, $convertedArguments);
            }

            case 'fatigue': {
                $functionName = "SetActorValue";
                $convertedArguments->add(new TES5String('Stamina'));
                $secondArg = $functionArguments->getValue(1);
                $convertedArguments->add($this->valueFactory->createValue($secondArg, $codeScope, $globalScope, $multipleScriptsScope));
                return $this->objectCallFactory->createObjectCall($calledOn, $functionName, $multipleScriptsScope, $convertedArguments);
            }

            case 'armorer': {
                $functionName = "SetActorValue";

                $convertedArguments->add(new TES5String("Smithing"));
                $secondArg = $functionArguments->getValue(1);
                $convertedArguments->add($this->valueFactory->createValue($secondArg, $codeScope, $globalScope, $multipleScriptsScope));
                return $this->objectCallFactory->createObjectCall($calledOn, $functionName, $multipleScriptsScope, $convertedArguments);
            }

            case 'security': {
                $functionName = "SetActorValue";
                $convertedArguments->add(new TES5String("Lockpicking"));
                $secondArg = $functionArguments->getValue(1);
                $convertedArguments->add($this->valueFactory->createValue($secondArg, $codeScope, $globalScope, $multipleScriptsScope));
                return $this->objectCallFactory->createObjectCall($calledOn, $functionName, $multipleScriptsScope, $convertedArguments);
            }

            case 'mercantile': { //It doesn't exist in Skyrim - defaulting to Illusion..
                $functionName = "SetActorValue";
                $convertedArguments->add(new TES5String("Speechcraft"));
                $secondArg = $functionArguments->getValue(1);
                $convertedArguments->add($this->valueFactory->createValue($secondArg, $codeScope, $globalScope, $multipleScriptsScope));
                return $this->objectCallFactory->createObjectCall($calledOn, $functionName, $multipleScriptsScope, $convertedArguments);
            }

            case 'mysticism': { //It doesn't exist in Skyrim - defaulting to Illusion..
                $functionName = "SetActorValue";
                $convertedArguments->add(new TES5String("Illusion"));
                $secondArg = $functionArguments->getValue(1);
                $convertedArguments->add($this->valueFactory->createValue($secondArg, $codeScope, $globalScope, $multipleScriptsScope));
                return $this->objectCallFactory->createObjectCall($calledOn, $functionName, $multipleScriptsScope, $convertedArguments);
            }

            case 'blade':
            case 'blunt': {
                $functionName = "SetActorValue";

                $convertedArguments->add(new TES5String("OneHanded"));
                $secondArg = $functionArguments->getValue(1);
                $convertedArguments->add($this->valueFactory->createValue($secondArg, $codeScope, $globalScope, $multipleScriptsScope));
                return $this->objectCallFactory->createObjectCall($calledOn, $functionName, $multipleScriptsScope, $convertedArguments);
            }

            case 'aggression': {
                $functionName = "SetActorValue";

                $secondArg = $functionArguments->getValue(1);
                $secondArgData = $secondArg->getData();
                if ($secondArgData == 0) {
                    $newValue = 0;
                } else if ($secondArgData > 0 && $secondArgData < 50) {
                    $newValue = 1;
                } else if ($secondArgData >= 50 and $secondArgData < 80) {
                    $newValue = 2;
                } else {
                    $newValue = 3;
                }

                $convertedArguments->add(new TES5String($firstArg->getData()));
                $convertedArguments->add(new TES5Float($newValue));
                return $this->objectCallFactory->createObjectCall($calledOn, $functionName, $multipleScriptsScope, $convertedArguments);
            }


            case 'confidence': {
                $functionName = "SetActorValue";

                $secondArg = $functionArguments->getValue(1);
                $secondArgData = $secondArg->getData();
                if ($secondArgData == 0) {
                    $newValue = 0;
                } else if ($secondArgData > 0 and $secondArgData < 30) {
                    $newValue = 1;
                } else if ($secondArgData >= 30 and $secondArgData < 70) {
                    $newValue = 2;
                } else if ($secondArgData >= 70 and $secondArgData < 99) {
                    $newValue = 3;
                } else {
                    $newValue = 4;
                }

                $convertedArguments->add(new TES5String($firstArg->getData()));
                $convertedArguments->add(new TES5Float($newValue));
                return $this->objectCallFactory->createObjectCall($calledOn, $functionName, $multipleScriptsScope, $convertedArguments);
            }


            default: {
                $functionName = "SetActorValue";
                $convertedArguments->add(new TES5String($firstArg->getData()));
                $secondArg = $functionArguments->getValue(1);
                $convertedArguments->add($this->valueFactory->createValue($secondArg, $codeScope, $globalScope, $multipleScriptsScope));
                return $this->objectCallFactory->createObjectCall($calledOn, $functionName, $multipleScriptsScope, $convertedArguments);
            }

        }


    }
}