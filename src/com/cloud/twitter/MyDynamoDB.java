package com.cloud.twitter;

// Copyright 2012-2015 Amazon.com, Inc. or its affiliates. All Rights Reserved.
// Licensed under the Apache License, Version 2.0.

import com.amazonaws.AmazonServiceException;
import com.amazonaws.auth.profile.ProfileCredentialsProvider;
import com.amazonaws.services.dynamodbv2.AmazonDynamoDBClient;
import com.amazonaws.services.dynamodbv2.document.DynamoDB;
import com.amazonaws.services.dynamodbv2.document.Item;
import com.amazonaws.services.dynamodbv2.document.Table;

public class MyDynamoDB {

    static DynamoDB dynamoDB = new DynamoDB(new AmazonDynamoDBClient(
            new ProfileCredentialsProvider()));
    static Table table;
    public MyDynamoDB(String tableName){
    	try{
    		table = dynamoDB.getTable(tableName);
    	}
    	catch(AmazonServiceException ase){
    		System.err.println("error: loading table");
    	}
    }

    public void addTweet(long tweetID, String user, String text, int favorite, int retweet, double longitude, double latitude){
    	try {
    		Item item = new Item()
    				.withPrimaryKey("TweetID", tweetID)
    				.withString("User", user)
    				.withString("Text", text)
    				.withNumber("Retweet", retweet)
    				.withNumber("Favorite", favorite)
    				.withNumber("longitude", longitude)
    				.withNumber("latitude", latitude);
    		table.putItem(item);

        } catch (AmazonServiceException ase) {
            System.err.println("error: failed to add tweet");
            ase.printStackTrace();
        }
    }
}