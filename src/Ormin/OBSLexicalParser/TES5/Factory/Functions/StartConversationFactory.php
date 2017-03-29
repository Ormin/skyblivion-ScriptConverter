<?php

namespace Ormin\OBSLexicalParser\TES5\Factory\Functions;

use Ormin\OBSLexicalParser\TES4\AST\Value\FunctionCall\TES4Function;
use Ormin\OBSLexicalParser\TES4\Context\ESMAnalyzer;
use Ormin\OBSLexicalParser\TES5\AST\Code\TES5CodeScope;
use Ormin\OBSLexicalParser\TES5\AST\Code\TES5Filler;
use Ormin\OBSLexicalParser\TES5\AST\Object\TES5Referencer;
use Ormin\OBSLexicalParser\TES5\AST\Scope\TES5GlobalScope;
use Ormin\OBSLexicalParser\TES5\AST\Scope\TES5MultipleScriptsScope;
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

class StartConversationFactory implements FunctionFactory
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
        /**
         * $md5 = 'TES4SCENE_' . substr(md5($calledOn->getReferencesTo()->getReferenceEdid() . $functionArguments->getValue(0)->getData()), 0, 16);
         * $sceneData = [$md5, $functionArguments->getValue(0)->getData()];
         * if($functionArguments->getValue(1) !== null)
         * {
         * $sceneData[] = $functionArguments->getValue(1)->getData();
         * }
         *
         * $this->metadataLogService->add('ADD_FORCEGREET_SCENE', $sceneData);
         * $reference = $this->referenceFactory->createReference($md5, $globalScope, $multipleScriptsScope, $localScope);
         * $funcArgs = new TES5ObjectCallArguments();
         * /**
         * Force start because in oblivion double using AddScriptPackage would actually overwrite the script package, so we mimic this
         * return $this->objectCallFactory->createObjectCall($reference, "ForceStart",$multipleScriptsScope, $funcArgs);
         */

        return new TES5Filler();
    }
}