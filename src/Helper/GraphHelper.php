<?php
/*
 * This file is part of the Bibliometric Snowballing project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace BS\Helper;


use BS\Model\Entity\Project;
use BS\Model\Entity\Work;

class GraphHelper
{
    /**
     * @var GraphHelper|null instance
     */
    protected static $instance = null;

    /**
     * @var array graph for visualization (needs to be built with getGraph()
     */
    protected $graph = array('nodes' => array(), 'edges' => array());

    /**
     * GraphHelper constructor.
     */
    private function __construct()
    {
    }

    /**
     * Returns the singleton.
     * @return GraphHelper instance
     */
    public static function instance()
    {
        if (!isset(GraphHelper::$instance)) {
            GraphHelper::$instance = new GraphHelper();
        }

        return GraphHelper::$instance;
    }

    /**
     * Builds and returns the graph for visualization.
     *
     * @param Project $project project entity
     * @return array graph
     */
    public function getGraph(Project $project)
    {
        // Iterate over all works in project.
        foreach ($project->getWorks() as $work) {
            /** @var Work $work */
            $doi = $work->getDoi();
            if ($doi === null) {
                continue;
            }

            $this->addNodeToGraphIfNotExistent($doi, $work->getTitle(), $this->getTooltipTitle($work));

            // Iterate over all referenced work DOIs.
            foreach ($work->getWorkDois() as $workDoi) {
                $referencedWork = Work::readByDoi($workDoi, false);
                $referencedLabel = $referencedWork !== null ? $referencedWork->getTitle() : $workDoi;
                $referencedTitle = $this->getTooltipTitle($referencedWork, 'DOI: ' . $workDoi);

                $this->addNodeToGraphIfNotExistent($workDoi, $referencedLabel, $referencedTitle);
                $this->addEdgeToGraphIfNotExistent($doi, $workDoi);
            }
        }

        return $this->graph;
    }

    /**
     * Returns a tooltip (vis.js title) for a work entity.
     *
     * @param Work|null $work work entity
     * @param string $fallback fallback, if work is null
     * @return string tooltip title
     */
    protected function getTooltipTitle($work, $fallback = '')
    {
        if ($work === null) {
            return $fallback;
        }

        return sprintf(
            'Title: %s<br>%sAuthors: %s<br>Year: %d<br>DOI: %s',
            $work->getTitle(),
            $work->getSubTitle() !== '' ? 'Subtitle: ' . $work->getSubTitle() . '<br>' : '',
            join(', ', $work->getAuthors()),
            $work->getWorkYear(),
            $work->getDoi()
        );
    }

    /**
     * Adds a node to the graph, if not existent.
     *
     * @param string $doi DOI of node
     * @param string $label optional label
     * @param string $title title (tooltip) for the node
     */
    protected function addNodeToGraphIfNotExistent($doi, $label = '', $title = '')
    {
        for ($i = 0; $i < count($this->graph['nodes']); $i++) {
            if ($this->graph['nodes'][$i]['id'] == $doi) {
                // Update label/title if necessary (e.g. when previous DOI reference was added, and now the full work
                // information is given).
                if ($this->graph['nodes'][$i]['label'] != $label) {
                    $this->graph['nodes'][$i]['label'] = $label;
                }
                if ($this->graph['nodes'][$i]['title'] != $title) {
                    $this->graph['nodes'][$i]['title'] = $title;
                }

                return;
            }
        }

        $this->graph['nodes'][] = array('id' => $doi, 'label' => $label, 'title' => $title);
    }

    /**
     * Adds an edge to the graph, if not existent.
     *
     * @param string $from from DOI
     * @param string $to to DOI
     */
    protected function addEdgeToGraphIfNotExistent($from, $to)
    {
        for ($i = 0; $i < count($this->graph['edges']); $i++) {
            if ($this->graph['edges'][$i]['from'] == $from && $this->graph['edges'][$i]['to'] == $to) {
                return;
            }
        }

        $this->graph['edges'][] = array('from' => $from, 'to' => $to, 'arrows' => 'to');
    }

    /**
     * Renders the graph to DOT language and returns the DOT content.
     *
     * @param Project $project project entity
     * @return string DOT content
     */
    public function getGraphAsDot(Project $project)
    {
        $graph = $this->getGraph($project);

        $nodeIds = array();
        $nodeLabels = array();
        $nodeTooltips = array();
        for ($i = 0; $i < count($graph['nodes']); $i++) {
            $nodeId = 'node' . $i;
            $nodeIds[$nodeId] = $graph['nodes'][$i]['id'];
            $nodeLabels[$nodeId] = $graph['nodes'][$i]['label'];
            $nodeTooltips[$nodeId] = str_replace('<br>', PHP_EOL, $graph['nodes'][$i]['title']);
        }

        $edges = array();
        foreach ($graph['edges'] as $edge) {
            $edges[$edge['from']][] = $edge['to'];
        }

        $dot = 'digraph {rankdir=LR;graph [splines=line, nodesep=0.5 ranksep=3.0]; '
            . 'node [shape=rectangle fontname="sans-serif" style="filled" color="lightgrey" fixedsize=true width=5.0 '
            . 'height=1.5]; edge [headport="w" tailport="e"]';
        foreach ($nodeIds as $nodeId => $nodeDoi) {
            $dot .= $nodeId . '[label="' . $this->getDotLineFeed($nodeLabels[$nodeId]) . '" tooltip="'
                . $nodeTooltips[$nodeId] . '"];' . PHP_EOL;
            if (isset($edges[$nodeDoi]) && count($edges[$nodeDoi]) > 0) {
                $dot .= $nodeId . ' -> { ';
                foreach ($edges[$nodeDoi] as $edge) {
                    $dot .= array_search($edge, $nodeIds) . ' ';
                }
                $dot .= '};';
            }
        }

        $dot .= '}';

        return $dot;
    }

    /**
     * Automatically line-feeds a text, when a line exceeds
     *
     * @param string $text string to get automatic line-feed
     * @param int $maxCharactersPerLine maximum of characters per line
     * @return string string with automatic line feeds
     */
    protected function getDotLineFeed($text, $maxCharactersPerLine = 38)
    {
        $output = '';
        $textParts = explode(' ', $text);
        $characters = 0;
        for ($i = 0; $i < count($textParts); $i++) {
            if (isset($textParts[$i+1]) && $characters + strlen($textParts[$i+1]) > $maxCharactersPerLine) {
                $output .= PHP_EOL;
                $characters = 0;
            }
            $output .= $textParts[$i] . ' ';
            $characters += strlen($textParts[$i]) + 1;
        }

        return $output;
    }

    /**
     * Returns the graph as SVG XML.
     *
     * @param Project $project project entity
     * @return null|string SVG XML or null
     */
    public function getGraphAsSvg(Project $project)
    {
        $tempFileName = tempnam('/tmp', uniqid());
        file_put_contents($tempFileName, $this->getGraphAsDot($project));
        $svgXml = `dot -Tsvg $tempFileName`;
        unlink($tempFileName);

        return $svgXml;
    }

    /**
     * Returns the graph as PNG image.
     *
     * @param Project $project project entity
     * @return null|string Base64 encoded PNG image or null
     */
    public function getGraphAsPng(Project $project)
    {
        $tempFileName = tempnam('/tmp', uniqid());
        file_put_contents($tempFileName, $this->getGraphAsDot($project));
        $pngData = `dot -Tpng $tempFileName`;
        unlink($tempFileName);

        return $pngData;
    }
}
