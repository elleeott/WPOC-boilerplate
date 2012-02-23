require 'sass'

module GETMODINT
	def file_url(staticFilePath,staticHost,filePath)
		assert_type filePath, :String
		filePath = filePath.value #get string value of literal
		staticFilePath = staticFilePath.value 
		staticHost = staticHost.value 
		modtime = File.mtime(filePath).to_i
		#Sass::Script::Number.new(modtime)
		
		fileBaseName = File.basename filePath, '.*'
		fileDir = File.dirname(filePath).sub(staticFilePath,'')
		fileExt = File.extname(filePath)
		path = "url('#{staticHost}#{fileDir}/#{fileBaseName}.#{modtime}#{fileExt}')"
		return Sass::Script::String.new(path)
	end
	Sass::Script::Functions.declare :modInt, [:filePath]
end

module Sass::Script::Functions
	include GETMODINT
end