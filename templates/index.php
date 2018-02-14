<?php $this->layout('layout', ['title' => 'Welcome to Bibliometric Snowballing']) ?>
<div class="container">
    <h2>What is Bibliometric Snowballing?</h2>
    <p>Bibliometric Snowballing is a new way to perform a <b>literature analysis</b>.
        You just have to identify some published documents like papers or articles and insert the data into this tool.
        We will use the references of your entries to create a list of all documents referenced.
        You will find new documents in that list, which might be relevant for your topic.
    </p>

    <h2>Why should I use it?</h2>
    <ul>
        <li>We find new literature based on your entries.</li>
        <li>You get suggestions for relevant documents.</li>
        <li>It's easy to use.</li>
        <li>It's free.</li>

    </ul>

    <h2>How does this app work?</h2>
    <p>There are a few basic steps to use this app.</p>
    <ol>
        <li><a href="/register">Register here.</a></li>
        <li><a href="/login">Sign in here</a></li>
        <li>Create and enter a new project. This will help you to seperate the topics of diffrent lists.</li>
        <li>Identify the documents which should be used as the basis for your literature analysis.
            The easiest way is to use the DOI - this is an unique identifier located on nearly all published papers.
            We will catch the data from your entry if you tell us the DOI, but you will have to enter them by yourself if you dont have the DOI.
        </li>
        <li>If you are ready for your first <b>analysis</b>, just hit "Start Snowballing Analysis"</li>
        <li>You get the results after a quick loadingpage. They are sorted by the document title, the authors or journals.</li>
        <li>You might add some of the identified documents to your project - just tick the checkbox aside the document and hit "Add documents"</li>
        <li>You can repeat these steps as often as you want.</li>
    </ol>
</div>
